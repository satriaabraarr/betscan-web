<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
        </div>
        <div>
            <div class="form-card-title">Input Content</div>
            <div class="form-card-sub">Text / image</div>
        </div>
    </div>

    {{-- Textarea teks --}}
    <div class="form-group">
        <label class="form-label" for="input_text">Text</label>
        <textarea id="input_text" name="input_text"
                  class="form-textarea"
                  placeholder="Type or paste text here..."></textarea>
    </div>

    <div class="input-divider">or</div>

    {{-- Drop zone gambar (multiple) --}}
    <div class="form-group">
        <label class="form-label">
            Image Upload
            <span style="font-size:.75rem;font-weight:400;color:var(--slate-400);margin-left:6px;">
                Max 10 images
            </span>
        </label>

        <div class="drop-zone" id="dropZone">
            {{-- multiple + name="input_images[]" --}}
            <input type="file" name="input_images[]" id="input_image"
                   class="drop-zone-input" accept=".png,.jpg,.jpeg" multiple>

            <div class="drop-zone-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
            </div>
            <div class="drop-zone-title">Drag &amp; Drop Images</div>
            <div class="drop-zone-sub">Or click to select files from device (can select multiple)</div>
            <div class="drop-zone-hint">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                Format: PNG, JPG, JPEG
            </div>
        </div>

        {{-- Grid preview multi-gambar --}}
        <div class="multi-preview-grid" id="multiPreviewGrid"></div>
    </div>
</div>