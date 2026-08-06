// ---------------------------------------------------------------
// Face Recognition — Kamera & Selfie + Upload Foto
// ---------------------------------------------------------------
const categoryInput = document.getElementById('user_category');
const faceVideo = document.getElementById('faceVideo');
const faceCanvas = document.getElementById('faceCanvas');
const faceResultInfo = document.getElementById('faceResultInfo');
const faceCapturedImg = document.getElementById('faceCapturedImg');

// State elements
const faceStateIdle = document.getElementById('faceStateIdle');
const faceStateCamera = document.getElementById('faceStateCamera');
const faceStateLoading = document.getElementById('faceStateLoading');
const faceStateResult = document.getElementById('faceStateResult');
const faceStateError = document.getElementById('faceStateError');

// Upload inputs (idle + error state)
const faceUploadInput = document.getElementById('faceUploadInput');
const faceUploadRetryInput = document.getElementById('faceUploadRetryInput');

let cameraStream = null;
let lastCaptureWasCamera = false; // track apakah foto terakhir dari kamera (untuk mirror)

// Tampilkan hanya satu state, sembunyikan yang lain
function showFaceState(stateName) {
    faceStateIdle.style.display = stateName === 'idle' ? '' : 'none';
    faceStateCamera.style.display = stateName === 'camera' ? '' : 'none';
    faceStateLoading.style.display = stateName === 'loading' ? '' : 'none';
    faceStateResult.style.display = stateName === 'result' ? '' : 'none';
    faceStateError.style.display = stateName === 'error' ? '' : 'none';
}

// Buka kamera
async function openCamera() {
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            audio: false,
        });
        faceVideo.srcObject = cameraStream;
        showFaceState('camera');
    } catch (err) {
        document.getElementById('faceErrorMsg').textContent =
            'Camera access denied. Please allow camera permission and try again.';
        showFaceState('error');
    }
}

// Hentikan kamera
function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
    faceVideo.srcObject = null;
}

// Render hasil sukses dari Flask ke state result
function renderFaceResult(data) {
    categoryInput.value = data.user_category;

    const categoryLabel = data.user_category === 'Pekerja'
        ? 'Worker <span style="font-size:.75rem;color:var(--slate-400)">(Age 25+)</span>'
        : 'Student / University Student <span style="font-size:.75rem;color:var(--slate-400)">(Age 7–24)</span>';

    const badgeClass = data.user_category === 'Pekerja' ? 'face-badge-worker' : 'face-badge-student';

    faceResultInfo.innerHTML = `
        <div class="face-result-category ${badgeClass}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ${categoryLabel}
        </div>
        <!--
        <div class="face-result-conf">
            Confidence: <strong>${(data.confidence * 100).toFixed(1)}%</strong>
        </div>-->`;

    showFaceState('result');
}

// Kirim file ke Flask /face/predict
async function sendToFacePredict(file, errorMsg = 'Failed to analyze face. Please try again.') {
    const formData = new FormData();
    formData.append('image', file, file.name);

    try {
        const res = await fetch(FACE_PREDICT_URL, { method: 'POST', body: formData });
        const data = await res.json();

        if (!res.ok) throw new Error(data.error ?? 'Server error');

        if (data.success === false && data.error_code === 'NO_FACE') {
            document.getElementById('faceErrorMsg').textContent =
                'No face detected. Please use a clearer photo or retake.';
            showFaceState('error');
            return;
        }

        renderFaceResult(data);

    } catch (err) {
        document.getElementById('faceErrorMsg').textContent = errorMsg;
        showFaceState('error');
    }
}

// Helper: baca file sebagai dataURL
function readFileAsDataUrl(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = e => resolve(e.target.result);
        reader.onerror = () => reject(new Error('Failed to read file'));
        reader.readAsDataURL(file);
    });
}

// Ambil frame dari video → Blob → kirim ke Flask
async function captureAndPredict() {
    faceCanvas.width = faceVideo.videoWidth || 640;
    faceCanvas.height = faceVideo.videoHeight || 480;
    faceCanvas.getContext('2d').drawImage(faceVideo, 0, 0);

    const dataUrl = faceCanvas.toDataURL('image/jpeg', 0.9);
    faceCapturedImg.src = dataUrl;
    lastCaptureWasCamera = true;

    stopCamera();
    showFaceState('loading');

    const blob = await (await fetch(dataUrl)).blob();
    const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });

    await sendToFacePredict(file, 'Failed to analyze face. Please try again.');
}

// Handle upload foto (dari input file mana saja)
async function handleUploadFile(file) {
    if (!file) return;

    const dataUrl = await readFileAsDataUrl(file);
    faceCapturedImg.src = dataUrl;
    lastCaptureWasCamera = false; // foto upload tidak perlu di-mirror

    showFaceState('loading');

    await sendToFacePredict(file, 'Failed to analyze the uploaded photo. Please try again.');
}

// Reset kamera ke state awal (idle)
function resetFaceState() {
    stopCamera();
    categoryInput.value = '';
    faceCapturedImg.src = '';
    faceResultInfo.innerHTML = '';
    faceUploadInput.value = '';
    faceUploadRetryInput.value = '';
    lastCaptureWasCamera = false;
    showFaceState('idle');
}

// ---------------------------------------------------------------
// Events — kamera
// ---------------------------------------------------------------
document.getElementById('btnOpenCamera').addEventListener('click', openCamera);
document.getElementById('btnRetryCamera').addEventListener('click', openCamera);
document.getElementById('btnCancelCamera').addEventListener('click', () => {
    stopCamera();
    showFaceState('idle');
});
document.getElementById('btnCapture').addEventListener('click', captureAndPredict);
document.getElementById('btnRetake').addEventListener('click', () => {
    categoryInput.value = '';
    resetFaceState();
});

// ---------------------------------------------------------------
// Events — upload foto
// ---------------------------------------------------------------
faceUploadInput.addEventListener('change', () => {
    if (faceUploadInput.files.length > 0) {
        handleUploadFile(faceUploadInput.files[0]);
        faceUploadInput.value = '';
    }
});

faceUploadRetryInput.addEventListener('change', () => {
    if (faceUploadRetryInput.files.length > 0) {
        handleUploadFile(faceUploadRetryInput.files[0]);
        faceUploadRetryInput.value = '';
    }
});

// Mirror foto kamera di state result (foto upload tidak perlu di-mirror)
const origShowFaceState = showFaceState;
// Terapkan/hapus class mirror pada faceCapturedImg saat masuk result
const _showFaceStateWithMirror = showFaceState;
document.getElementById('btnCapture') // hook saat capture selesai ditangani di captureAndPredict

// ---------------------------------------------------------------
// Multi-image upload & preview
// ---------------------------------------------------------------
const dropZone = document.getElementById('dropZone');
const inputImage = document.getElementById('input_image');
const multiPreviewGrid = document.getElementById('multiPreviewGrid');

let selectedFiles = [];

function renderPreviewGrid() {
    multiPreviewGrid.innerHTML = '';

    selectedFiles.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const item = document.createElement('div');
            item.className = 'preview-item';
            item.dataset.idx = idx;
            item.innerHTML = `
                <img src="${e.target.result}" alt="${file.name}">
                <button type="button" class="preview-item-remove" data-idx="${idx}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="preview-item-name">${file.name}</div>
            `;
            multiPreviewGrid.appendChild(item);
        };
        reader.readAsDataURL(file);
    });

    syncFilesToInput();
}

function syncFilesToInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    inputImage.files = dt.files;
}

function addFiles(newFiles) {
    const MAX_FILES = 10;
    const existing = new Set(selectedFiles.map(f => f.name + f.size));

    for (const file of newFiles) {
        if (selectedFiles.length >= MAX_FILES) {
            showToast(`Maximum ${MAX_FILES} images allowed.`, 'error');
            break;
        }
        const key = file.name + file.size;
        if (!existing.has(key)) {
            selectedFiles.push(file);
            existing.add(key);
        }
    }
    renderPreviewGrid();
}

function removeFile(idx) {
    selectedFiles.splice(idx, 1);
    renderPreviewGrid();
}

inputImage.addEventListener('change', () => {
    if (inputImage.files.length > 0) {
        addFiles(Array.from(inputImage.files));
        inputImage.value = '';
    }
});

dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('drag-over');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    if (e.dataTransfer.files.length > 0) addFiles(Array.from(e.dataTransfer.files));
});

multiPreviewGrid.addEventListener('click', e => {
    const btn = e.target.closest('.preview-item-remove');
    if (btn) removeFile(parseInt(btn.dataset.idx));
});

// ---------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------
function vulnLabel(v) {
    const map = { TINGGI: 'High', RENDAH: 'Low' };
    return map[v] ?? v;
}

function modelChipHtml(label, result) {
    if (result === 'not_run') {
        return `<span class="model-chip not-run">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>${label}: Not Run</span>`;
    }
    const isJudi = result === 'judi';
    const cls = isJudi ? 'judi' : 'non-judi';
    const path = isJudi
        ? `<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>`
        : `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`;
    return `<span class="model-chip ${cls}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">${path}</svg>
        ${label}: ${isJudi ? 'Detected Gambling' : 'Not Detected'}</span>`;
}

function buildKalimatJudiHtml(nlpDetail) {
    const judis = (nlpDetail ?? []).filter(d => d.result === 'judi');
    if (judis.length === 0) return '';

    const items = judis.map((d, i) => `
        <div class="kalimat-judi-item">
            <span class="kalimat-judi-badge">#${i + 1}</span>
            <span>${d.kalimat}</span>
            <span class="kalimat-judi-conf">${(d.prob_judi * 100).toFixed(1)}%</span>
        </div>`
    ).join('');

    return `
        <div class="kalimat-judi-wrap">
            <div class="kalimat-judi-title">
                <span>Text Detected as Gambling</span>
                <span class="kalimat-judi-count">${judis.length} Sentence${judis.length > 1 ? 's' : ''}</span>
            </div>
            <div class="kalimat-judi-list">${items}</div>
        </div>`;
}

function buildCnnImageResultsHtml(cnnImageResults) {
    if (!cnnImageResults || cnnImageResults.length === 0) return '';

    const judiImages = cnnImageResults.filter(img => img.result === 'judi');
    if (judiImages.length === 0) return '';

    const cards = judiImages.map((img, i) => {
        const imgTag = img.annotated_image
            ? `<img src="${img.annotated_image}" alt="Image ${i + 1} detection result"
                    class="cnn-result-img" onclick="openLightbox('${img.annotated_image}')" title="Click to zoom">`
            : `<div class="cnn-result-img-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    <span>No detection</span>
               </div>`;

        return `
            <div class="cnn-result-card cnn-card-judi">
                <div class="cnn-result-img-wrap">
                    ${imgTag}
                    <div class="cnn-zoom-hint">Click to zoom</div>
                </div>
                <div class="cnn-result-info">
                    <div class="cnn-result-filename">${img.filename || `Image ${i + 1}`}</div>
                    <span class="cnn-result-badge badge-judi">⚠ Gambling Detected</span>
                    <div class="cnn-result-conf">Confidence: ${(img.confidence * 100).toFixed(1)}%</div>
                </div>
            </div>`;
    }).join('');

    return `
        <div class="cnn-multi-wrap">
            <div class="cnn-multi-header">
                <span class="cnn-multi-title">
                    Image Detected as Gambling
                    <span class="cnn-multi-count">${judiImages.length} Image${judiImages.length > 1 ? 's' : ''}</span>
                </span>
            </div>
            <div class="cnn-result-grid">${cards}</div>
        </div>`;
}

// ---------------------------------------------------------------
// Lightbox zoom
// ---------------------------------------------------------------
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('imgLightbox').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event) {
    if (event.target === document.getElementById('imgLightbox')) {
        document.getElementById('imgLightbox').classList.remove('show');
        document.body.style.overflow = '';
    }
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('imgLightbox').classList.remove('show');
        document.body.style.overflow = '';
    }
});

// ---------------------------------------------------------------
// Render hasil deteksi
// ---------------------------------------------------------------
function renderHasil(data) {
    const d = data.data;
    const label = d.detail_label;
    const colorClass = label.color;

    const icons = {
        'shield-x': `<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z"/>`,
        'alert-triangle': `<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>`,
        'shield-check': `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>`,
    };

    const iconPath = icons[label.icon] ?? icons['shield-check'];

    const html = `
        <div class="hasil-banner ${colorClass}">
            <div class="hasil-banner-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">${iconPath}</svg>
            </div>
            <div>
                <div class="hasil-banner-title">${label.label}</div>
                <span class="hasil-banner-level">${label.level}</span>
            </div>
        </div>
        <div class="hasil-body">
            <div class="hasil-scores">
                <div class="score-box">
                    <div class="score-box-label">Content Status</div>
                    <div class="score-box-val" style="font-size:1rem;">${d.content_detected ? 'Gambling Detected' : 'No Gambling Detected'}</div>
                    <div class="score-box-sub">${d.content_risk_level}</div>
                </div>
                <div class="score-box">
                    <div class="score-box-label">User Vulnerability Level</div>
                    <div class="score-box-val" style="font-size:1rem;">${vulnLabel(d.user_vulnerability)}</div>
                    <div class="score-box-sub">Your occupation places you in this vulnerability level</div>
                </div>
            </div>
            <div class="model-results">
                ${d.input_type !== 'image' ? modelChipHtml('NLP Model', d.nlp_result) : ''}
                ${d.input_type !== 'text' ? modelChipHtml('CNN Model', d.cnn_result) : ''}
            </div>
            ${d.input_type !== 'image' ? buildKalimatJudiHtml(d.nlp_detail) : ''}
            ${d.input_type !== 'text' ? buildCnnImageResultsHtml(d.cnn_image_results) : ''}
            <div class="edukasi-box">
                <div class="edukasi-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    Educational Explanation
                </div>
                <div class="edukasi-text">${d.education}</div>
            </div>
        </div>`;

    document.getElementById('hasilPlaceholder').style.display = 'none';
    const hasilContent = document.getElementById('hasilContent');
    hasilContent.innerHTML = html;
    hasilContent.style.display = 'block';
    document.getElementById('resetWrap').style.display = 'block';
    document.getElementById('hasilWrap').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ---------------------------------------------------------------
// Form submit (AJAX)
// ---------------------------------------------------------------
document.getElementById('detectionForm').addEventListener('submit', async function () {
    const btn = document.getElementById('btnSubmit');
    const hasText = document.getElementById('input_text').value.trim() !== '';
    const hasImage = selectedFiles.length > 0;

    if (!categoryInput.value) {
        showToast('Please complete the face scan first to detect your user category.', 'error');
        return;
    }

    if (!hasText && !hasImage) {
        showToast('Please enter text or upload at least one image.', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = `<div class="spinner"></div> Processing...`;

    try {
        const formData = new FormData(this);
        formData.delete('input_images[]');
        selectedFiles.forEach(file => formData.append('input_images[]', file, file.name));

        const res = await fetch(DETECT_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData,
        });
        const data = await res.json();

        if (data.success) {
            renderHasil(data);
        } else {
            showToast(data.message ?? 'An error occurred. Please try again.', 'error');
        }
    } catch (err) {
        showToast('Failed to connect to server. Check your connection.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="section-eyebrow-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M2.77 10C2.34 10 2 9.66 2 9.23V6.92C2 4.21 4.21 2 6.92 2H9.23C9.66 2 10 2.34 10 2.77C10 3.2 9.66 3.54 9.23 3.54H6.92C5.05 3.54 3.54 5.06 3.54 6.92V9.23C3.54 9.66 3.19 10 2.77 10Z" fill="currentColor"/>
                <path d="M21.23 10C20.81 10 20.46 9.66 20.46 9.23V6.92C20.46 5.05 18.94 3.54 17.08 3.54H14.77C14.34 3.54 14 3.19 14 2.77C14 2.35 14.34 2 14.77 2H17.08C19.79 2 22 4.21 22 6.92V9.23C22 9.66 21.66 10 21.23 10Z" fill="currentColor"/>
                <path d="M17.0799 22.0002H15.6899C15.2699 22.0002 14.9199 21.6602 14.9199 21.2302C14.9199 20.8102 15.2599 20.4602 15.6899 20.4602H17.0799C18.9499 20.4602 20.4599 18.9402 20.4599 17.0802V15.7002C20.4599 15.2802 20.7999 14.9302 21.2299 14.9302C21.6499 14.9302 21.9999 15.2702 21.9999 15.7002V17.0802C21.9999 19.7902 19.7899 22.0002 17.0799 22.0002Z" fill="currentColor"/>
                <path d="M9.23 22H6.92C4.21 22 2 19.79 2 17.08V14.77C2 14.34 2.34 14 2.77 14C3.2 14 3.54 14.34 3.54 14.77V17.08C3.54 18.95 5.06 20.46 6.92 20.46H9.23C9.65 20.46 10 20.8 10 21.23C10 21.66 9.66 22 9.23 22Z" fill="currentColor"/>
                <path d="M18.4595 11.23H5.53953C5.10953 11.23 4.76953 11.58 4.76953 12C4.76953 12.42 5.10953 12.77 5.53953 12.77H18.4595C18.8895 12.77 19.2295 12.42 19.2295 12C19.2295 11.58 18.8895 11.23 18.4595 11.23Z" fill="currentColor"/>
                <path d="M6.90039 13.94V14.27C6.90039 15.93 8.24039 17.27 9.90039 17.27H14.1004C15.7604 17.27 17.1004 15.93 17.1004 14.27V13.94C17.1004 13.82 17.0104 13.73 16.8904 13.73H7.11039C6.99039 13.73 6.90039 13.82 6.90039 13.94Z" fill="currentColor"/>
                <path d="M6.90039 10.06V9.72998C6.90039 8.06998 8.24039 6.72998 9.90039 6.72998H14.1004C15.7604 6.72998 17.1004 8.06998 17.1004 9.72998V10.06C17.1004 10.18 17.0104 10.27 16.8904 10.27H7.11039C6.99039 10.27 6.90039 10.18 6.90039 10.06Z" fill="currentColor"/>
            </svg>
            Get Detection Result`;
    }
});

// ---------------------------------------------------------------
// Reset form & hasil
// ---------------------------------------------------------------
document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'btnReset') {
        document.getElementById('detectionForm').reset();
        document.getElementById('input_text').value = '';

        resetFaceState();

        selectedFiles = [];
        multiPreviewGrid.innerHTML = '';
        inputImage.value = '';

        document.getElementById('hasilContent').style.display = 'none';
        document.getElementById('hasilContent').innerHTML = '';
        document.getElementById('hasilPlaceholder').style.display = 'block';
        document.getElementById('resetWrap').style.display = 'none';

        document.getElementById('mulai-deteksi').scrollIntoView({ behavior: 'smooth' });
    }
});