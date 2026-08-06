<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
        </div>
        <div>
            <div class="form-card-title">User Profile</div>
            <div class="form-card-sub">Face recognition to detect user category</div>
        </div>
    </div>

    {{-- Hidden input — diisi otomatis oleh JS setelah face predict --}}
    <input type="hidden" name="user_category" id="user_category" required>

    {{-- STATE 1: Tombol buka kamera / upload foto (tampil di awal) --}}
    <div id="faceStateIdle">
        <div class="face-idle-wrap">
            <div class="face-idle-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                </svg>
            </div>
            <div class="face-idle-title">Face Scan Required</div>
            <div class="face-idle-sub">Take a selfie or upload a photo to detect your user category automatically.</div>

            {{-- Opsi 1: Buka Kamera --}}
            <button type="button" class="btn-open-camera" id="btnOpenCamera">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                </svg>
                Open Camera
            </button>

            {{-- Divider --}}
            <div class="face-idle-divider">or</div>

            {{-- Opsi 2: Upload Foto --}}
            <label class="btn-upload-photo" for="faceUploadInput">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
                Upload Photo
            </label>
            <input type="file" id="faceUploadInput" accept="image/png,image/jpg,image/jpeg" style="display:none;">
        </div>
    </div>

    {{-- STATE 2: Live kamera + tombol capture --}}
    <div id="faceStateCamera" style="display:none;">
        <div class="face-camera-wrap">
            <div class="face-video-wrap">
                <video id="faceVideo" class="face-video" autoplay playsinline muted></video>
                <div class="face-video-overlay">
                    <div class="face-guide-circle"></div>
                </div>
            </div>
            <div class="face-camera-hint">Position your face inside the circle, then tap capture.</div>
            <div class="face-camera-actions">
                <button type="button" class="btn-capture" id="btnCapture">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                    </svg>
                    Capture
                </button>
                <button type="button" class="btn-cancel-camera" id="btnCancelCamera">Cancel</button>
            </div>
        </div>
        {{-- Canvas tersembunyi untuk ambil frame dari video --}}
        <canvas id="faceCanvas" style="display:none;"></canvas>
    </div>

    {{-- STATE 3: Loading — mengirim foto ke Flask --}}
    <div id="faceStateLoading" style="display:none;">
        <div class="face-loading-wrap">
            <div class="face-loading-spinner"></div>
            <div class="face-loading-text">Analyzing face...</div>
        </div>
    </div>

    {{-- STATE 4: Hasil deteksi wajah --}}
    <div id="faceStateResult" style="display:none;">
        <div class="face-result-wrap">
            <img id="faceCapturedImg" class="face-captured-img" src="" alt="Face photo">
            <div class="face-result-info" id="faceResultInfo">
                {{-- Diisi oleh JS --}}
            </div>
            <button type="button" class="btn-retake" id="btnRetake">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                Retake / Re-upload
            </button>
        </div>
    </div>

    {{-- STATE 5: Error (no face / kamera tidak bisa dibuka) --}}
    <div id="faceStateError" style="display:none;">
        <div class="face-error-wrap">
            <div class="face-error-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
            <div class="face-error-msg" id="faceErrorMsg">An error occurred.</div>
            <div class="face-error-actions">
                <button type="button" class="btn-open-camera" id="btnRetryCamera">Try Camera Again</button>
                <label class="btn-upload-photo" for="faceUploadRetryInput">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    Upload Instead
                </label>
                <input type="file" id="faceUploadRetryInput" accept="image/png,image/jpg,image/jpeg" style="display:none;">
            </div>
        </div>
    </div>

</div>