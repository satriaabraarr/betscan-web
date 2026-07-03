<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detections', function (Blueprint $table) {
            $table->id();

            // Profil Pengguna
            // (user_age dihapus — kerentanan pengguna kini ditentukan langsung dari kategori)
            $table->enum('user_category', ['Pelajar/Mahasiswa', 'Pekerja']);

            // Input Konten
            $table->text('input_text')->nullable();
            $table->string('input_image_path')->nullable();
            $table->enum('input_type', ['text', 'image', 'both']);

            // Hasil Deteksi Model (diisi setelah model ML siap)
            $table->enum('nlp_result', ['judi', 'non_judi', 'not_run'])->default('not_run');
            $table->enum('cnn_result', ['judi', 'non_judi', 'not_run'])->default('not_run');

            // Status Deteksi Konten
            // Konten dianggap mengandung judi jika minimal satu model (NLP/CNN) positif
            $table->boolean('content_detected')->default(false);

            // Kategorisasi
            // Kerentanan ditentukan langsung dari kategori pengguna:
            // Pelajar/Mahasiswa -> TINGGI, Pekerja -> RENDAH
            $table->enum('user_vulnerability', [
                'TINGGI', 'RENDAH'
            ])->nullable();

            // Level risiko konten untuk label tampilan (derivasi dari content_detected)
            $table->enum('content_risk_level', ['KONTEN_BERISIKO', 'KONTEN_AMAN'])->nullable();

            // Rekomendasi CBF — hanya dua tindakan untuk konten terdeteksi judi,
            // ditambah AMAN untuk konten yang tidak terdeteksi judi sama sekali
            $table->enum('recommendation', [
                'BLOKIR', 'BERI_PERINGATAN', 'AMAN'
            ])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
