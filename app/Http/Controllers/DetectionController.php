<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DetectionController extends Controller
{
    private const PYTHON_API = 'http://127.0.0.1:8001';

    // =========================================================
    // Halaman Utama
    // =========================================================
    public function index()
    {
        return view('welcome');
    }

    // =========================================================
    // Proses Deteksi
    // =========================================================
    public function detect(Request $request)
    {
        $request->validate([
            // user_age dihapus — profil pengguna kini hanya menggunakan kategori
            'user_category'   => 'required|in:Pelajar/Mahasiswa,Pekerja',
            'input_text'      => 'nullable|string|max:50000',
            // Validasi array gambar: tiap file maks 5MB, format PNG/JPG
            'input_images'    => 'nullable|array|max:10',
            'input_images.*'  => 'image|mimes:png,jpg,jpeg|max:5120',
        ]);

        $hasText   = $request->filled('input_text');
        $hasImages = $request->hasFile('input_images');

        if (!$hasText && !$hasImages) {
            return response()->json([
                'success' => false,
                'message' => 'Enter text or upload at least one image.',
            ], 422);
        }

        // Tentukan input type
        if ($hasText && $hasImages) {
            $inputType = 'both';
        } elseif ($hasText) {
            $inputType = 'text';
        } else {
            $inputType = 'image';
        }

        // Simpan semua gambar ke storage
        $imagePaths = [];
        if ($hasImages) {
            foreach ($request->file('input_images') as $imageFile) {
                $imagePaths[] = $imageFile->store('detections', 'public');
            }
        }

        // =========================================================
        // Panggil Model ML
        // =========================================================
        $nlpResult      = 'not_run';
        $cnnResult      = 'not_run';
        $nlpDetail      = [];
        $cnnImageResults = [];   // hasil per-gambar dari CNN

        if ($hasText) {
            $nlpRaw    = $this->callNlpModel($request->input('input_text'));
            $nlpResult = $nlpRaw['result'];
            $nlpDetail = $nlpRaw['detail'];
        }

        if ($hasImages) {
            $cnnRaw          = $this->callCnnModel($request->file('input_images'));
            $cnnResult       = $cnnRaw['overall_result'];
            $cnnImageResults = $cnnRaw['results'];
        }

        // =========================================================
        // Evaluasi Hasil Deteksi
        // Konten dianggap mengandung judi jika minimal satu model (NLP/CNN) positif
        // =========================================================
        $contentDetected = ($nlpResult === 'judi') || ($cnnResult === 'judi');

        // Kedua jalur (konten terdeteksi judi maupun konten aman) tetap melalui
        // tahap Kategorisasi Pengguna -> Kategorisasi Konten -> CBF / Matriks Keputusan.
        $userVulnerability = $this->categorizeUser($request->user_category);
        $contentRiskLevel  = $this->categorizeContent($contentDetected);
        $recommendation    = $this->applyDecisionMatrix($contentRiskLevel, $userVulnerability);

        // Simpan ke database
        Detection::create([
            'user_category'       => $request->user_category,
            'input_text'          => $request->input_text,
            'input_image_path'    => !empty($imagePaths) ? $imagePaths[0] : null,
            'input_type'          => $inputType,
            'nlp_result'          => $nlpResult,
            'cnn_result'          => $cnnResult,
            'content_detected'    => $contentDetected,
            'user_vulnerability'  => $userVulnerability,
            'content_risk_level'  => $contentRiskLevel,
            'recommendation'      => $recommendation,
        ]);

        $contentRiskLevelDisplay = match ($contentRiskLevel) {
            'KONTEN_BERISIKO' => 'Risky Content',
            'KONTEN_AMAN'     => 'Safe Content',
            default           => $contentRiskLevel,
        };

        return response()->json([
            'success' => true,
            'data'    => [
                'recommendation'      => $recommendation,
                'content_detected'    => $contentDetected,
                'user_vulnerability'  => $userVulnerability,
                'content_risk_level'  => $contentRiskLevelDisplay,
                'nlp_result'          => $nlpResult,
                'nlp_detail'          => $nlpDetail,
                'cnn_result'          => $cnnResult,
                'cnn_image_results'   => $cnnImageResults,  // per-gambar
                'input_type'          => $inputType,
                'education'           => $this->getEducationText(
                    $recommendation, $userVulnerability, $request->user_category
                ),
                'detail_label'  => $this->getRecommendationLabel($recommendation),
                'pending_model' => false,
            ],
        ]);
    }

    // =========================================================
    // Panggil Model NLP
    // =========================================================
    private function callNlpModel(string $text): array
    {
        try {
            $response = Http::timeout(180)
                ->asForm()
                ->post(self::PYTHON_API . '/nlp/predict', ['text' => $text]);

            if ($response->successful()) {
                $json   = $response->json();
                $result = $json['result'] ?? 'non_judi';

                return [
                    'result'        => in_array($result, ['judi', 'non_judi']) ? $result : 'non_judi',
                    'total_kalimat' => $json['total_kalimat'] ?? 0,
                    'kalimat_judi'  => $json['kalimat_judi']  ?? 0,
                    'detail'        => $json['detail']        ?? [],
                ];
            }

            Log::warning('NLP API response tidak sukses', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memanggil NLP API', ['error' => $e->getMessage()]);
        }

        return ['result' => 'non_judi', 'total_kalimat' => 0, 'kalimat_judi' => 0, 'detail' => []];
    }

    // =========================================================
    // Panggil Model CNN
    // =========================================================
    private function callCnnModel(array $imageFiles): array
    {
        try {
            // Buat HTTP request dengan multiple file attachment
            $http = Http::timeout(120);

            foreach ($imageFiles as $idx => $imageFile) {
                $http = $http->attach(
                    'images[]',                              // ← field name array
                    file_get_contents($imageFile->getRealPath()),
                    $imageFile->getClientOriginalName() ?: "image_{$idx}.jpg",
                    ['Content-Type' => $imageFile->getMimeType()]
                );
            }

            $response = $http->post(self::PYTHON_API . '/cnn/predict');

            if ($response->successful()) {
                $json           = $response->json();
                $overallResult  = $json['overall_result'] ?? 'non_judi';
                $overallResult  = in_array($overallResult, ['judi', 'non_judi']) ? $overallResult : 'non_judi';

                Log::info('CNN predict berhasil', [
                    'overall'     => $overallResult,
                    'judi_count'  => $json['judi_count'] ?? 0,
                    'total'       => $json['total_images'] ?? 0,
                ]);

                return [
                    'overall_result' => $overallResult,
                    'judi_count'     => $json['judi_count']  ?? 0,
                    'total_images'   => $json['total_images'] ?? 0,
                    'results'        => $json['results']      ?? [],
                ];
            }

            Log::warning('CNN API response tidak sukses', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memanggil CNN API', ['error' => $e->getMessage()]);
        }

        return [
            'overall_result' => 'non_judi',
            'judi_count'     => 0,
            'total_images'   => 0,
            'results'        => [],
        ];
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Kategorisasi tingkat kerentanan pengguna langsung berdasarkan kategori:
     * Pelajar/Mahasiswa -> TINGGI, Pekerja -> RENDAH
     */
    private function categorizeUser(string $category): string
    {
        return match ($category) {
            'Pelajar/Mahasiswa' => 'TINGGI',
            'Pekerja'           => 'RENDAH',
            default             => 'RENDAH',
        };
    }

    /**
     * Matriks Keputusan Content-Based Filtering.
     * Mencocokkan content_risk_level dengan user_vulnerability:
     * - KONTEN_AMAN                         -> AMAN (berlaku untuk semua tingkat kerentanan)
     * - KONTEN_BERISIKO + TINGGI (Pelajar/Mahasiswa) -> BLOKIR
     * - KONTEN_BERISIKO + RENDAH (Pekerja)           -> BERI_PERINGATAN
     */
    private function applyDecisionMatrix(string $contentRiskLevel, string $userVulnerability): string
    {
        if ($contentRiskLevel === 'KONTEN_AMAN') {
            return 'AMAN';
        }

        if ($userVulnerability === 'TINGGI') {
            return 'BLOKIR';
        }
        return 'BERI_PERINGATAN';
    }

    /**
     * Label level risiko konten untuk tampilan, diturunkan dari status deteksi.
     */
    private function categorizeContent(bool $contentDetected): string
    {
        return $contentDetected ? 'KONTEN_BERISIKO' : 'KONTEN_AMAN';
    }

    private function getEducationText(string $rec, string $vuln, string $cat): string
    {
        $vulnLabel = match ($vuln) {
            'TINGGI' => 'high',
            default  => 'low',
        };

        $catLabel = match ($cat) {
            'Pelajar/Mahasiswa' => 'Student/University Student',
            'Pekerja'           => 'Worker',
            default             => $cat,
        };

        if ($rec === 'BLOKIR') {
            return "This content is detected to contain online gambling elements. As a {$catLabel}, you belong to the {$vulnLabel} vulnerability group that requires maximum protection. Online gambling can cause addiction, financial loss, and serious psychological impact. Immediately block or avoid this content.";
        }
        if ($rec === 'BERI_PERINGATAN') {
            return "This content contains indications of online gambling elements. As a {$catLabel}, you have a {$vulnLabel} level of vulnerability, but stay alert. Online gambling has real risks to finances and mental health. Consider avoiding this content.";
        }
        return "The content you entered was not detected to contain online gambling elements. Stay alert and wise in accessing digital content.";
    }

    private function getRecommendationLabel(string $rec): array
    {
        if ($rec === 'BLOKIR') {
            return ['label' => 'Block Content',  'level' => 'Immediate Action',   'color' => 'danger',  'icon' => 'shield-x'];
        }
        if ($rec === 'BERI_PERINGATAN') {
            return ['label' => 'Give Warning',   'level' => 'Caution Advised', 'color' => 'warning', 'icon' => 'alert-triangle'];
        }
        return ['label' => 'Safe Content', 'level' => 'No Action Needed', 'color' => 'success', 'icon' => 'shield-check'];
    }
}
