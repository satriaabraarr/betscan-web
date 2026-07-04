<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
        </div>
        <div>
            <div class="form-card-title">User Information</div>
            <div class="form-card-sub">User Profile</div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="user_category">User Category <span class="req">*</span></label>
        <div class="custom-select-wrap" id="categorySelect">
            <input type="hidden" name="user_category" id="user_category" required>
            <div class="custom-select-trigger" id="categoryTrigger">
                <span id="categoryLabel">Select user category</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </div>
            <div class="custom-select-dropdown" id="categoryDropdown">
                <div class="custom-select-option" data-value="Pelajar/Mahasiswa" data-label="Student / University Student (Age 7 - 24)">
                    Student / University Student (Age 7 - 24)
                    <svg class="opt-check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
                <div class="custom-select-option" data-value="Pekerja" data-label="Worker (Age 25+)">
                    Worker (Age 25+)
                    <svg class="opt-check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
