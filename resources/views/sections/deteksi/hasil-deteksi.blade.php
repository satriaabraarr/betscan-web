<div class="form-card" style="margin-top:1.25rem;padding:0;overflow:hidden;" id="hasilWrap">
    <div class="form-card-header hasil-card-header" style="padding:1.25rem 1.75rem;border-bottom:1px solid var(--slate-100);margin-bottom:0;">
        <div class="form-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
        </div>
        <div>
            <div class="form-card-title">Detection Result</div>
            <div class="form-card-sub">ML analysis will appear here</div>
        </div>
    </div>

    <div class="hasil-placeholder" id="hasilPlaceholder">
        <div class="hasil-placeholder-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
        </div>
        <div class="hasil-placeholder-title">Detection Result</div>
        <div class="hasil-placeholder-sub">Fill in the form above to see the detection results.</div>
    </div>

    <div id="hasilContent" style="display:none;"></div>
</div>

<div id="resetWrap" style="display:none;">
    <button type="button" class="btn-reset" id="btnReset">
        <svg viewBox="0 0 24 24" fill="none">
            <path d="M12.0001 22.75C6.80008 22.75 2.58008 18.52 2.58008 13.33C2.58008 8.13999 6.80008 3.89999 12.0001 3.89999C13.0701 3.89999 14.1101 4.04999 15.1101 4.35999C15.5101 4.47999 15.7301 4.89999 15.6101 5.29999C15.4901 5.69999 15.0701 5.91999 14.6701 5.79999C13.8201 5.53999 12.9201 5.39999 12.0001 5.39999C7.63008 5.39999 4.08008 8.94999 4.08008 13.32C4.08008 17.69 7.63008 21.24 12.0001 21.24C16.3701 21.24 19.9201 17.69 19.9201 13.32C19.9201 11.74 19.4601 10.22 18.5901 8.91999C18.3601 8.57999 18.4501 8.10999 18.8001 7.87999C19.1401 7.64999 19.6101 7.73999 19.8401 8.08999C20.8801 9.63999 21.4301 11.45 21.4301 13.33C21.4201 18.52 17.2001 22.75 12.0001 22.75Z" fill="currentColor"/>
            <path d="M16.13 6.06999C15.92 6.06999 15.71 5.97999 15.56 5.80999L12.67 2.49C12.4 2.18 12.43 1.69999 12.74 1.42999C13.05 1.15999 13.53 1.18999 13.8 1.49999L16.69 4.82C16.96 5.13 16.93 5.60999 16.62 5.87999C16.49 6.00999 16.31 6.06999 16.13 6.06999Z" fill="currentColor"/>
            <path d="M12.7602 8.52999C12.5302 8.52999 12.3002 8.41999 12.1502 8.21999C11.9102 7.88999 11.9802 7.41999 12.3102 7.16999L15.6802 4.70999C16.0102 4.45999 16.4802 4.53999 16.7302 4.86999C16.9802 5.19999 16.9002 5.66999 16.5702 5.91999L13.2002 8.38999C13.0702 8.48999 12.9202 8.52999 12.7602 8.52999Z" fill="currentColor"/>
        </svg>
        Detect Another Text or Image
    </button>
</div>