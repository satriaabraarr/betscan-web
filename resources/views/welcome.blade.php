<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>BetScan AI - Detection Gambling Content</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beranda.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tentang.css') }}">
    <link rel="stylesheet" href="{{ asset('css/teknologi.css') }}">
    <link rel="stylesheet" href="{{ asset('css/deteksi.css') }}">
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar" id="navbar">
    <a href="#beranda" class="nav-logo">
        <div class="nav-logo-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.77 10C2.34 10 2 9.66 2 9.23V6.92C2 4.21 4.21 2 6.92 2H9.23C9.66 2 10 2.34 10 2.77C10 3.2 9.66 3.54 9.23 3.54H6.92C5.05 3.54 3.54 5.06 3.54 6.92V9.23C3.54 9.66 3.19 10 2.77 10Z" fill="currentColor"/>
                <path d="M21.23 10C20.81 10 20.46 9.66 20.46 9.23V6.92C20.46 5.05 18.94 3.54 17.08 3.54H14.77C14.34 3.54 14 3.19 14 2.77C14 2.35 14.34 2 14.77 2H17.08C19.79 2 22 4.21 22 6.92V9.23C22 9.66 21.66 10 21.23 10Z" fill="currentColor"/>
                <path d="M17.0799 22.0002H15.6899C15.2699 22.0002 14.9199 21.6602 14.9199 21.2302C14.9199 20.8102 15.2599 20.4602 15.6899 20.4602H17.0799C18.9499 20.4602 20.4599 18.9402 20.4599 17.0802V15.7002C20.4599 15.2802 20.7999 14.9302 21.2299 14.9302C21.6499 14.9302 21.9999 15.2702 21.9999 15.7002V17.0802C21.9999 19.7902 19.7899 22.0002 17.0799 22.0002Z" fill="currentColor"/>
                <path d="M9.23 22H6.92C4.21 22 2 19.79 2 17.08V14.77C2 14.34 2.34 14 2.77 14C3.2 14 3.54 14.34 3.54 14.77V17.08C3.54 18.95 5.06 20.46 6.92 20.46H9.23C9.65 20.46 10 20.8 10 21.23C10 21.66 9.66 22 9.23 22Z" fill="currentColor"/>
                <path d="M18.4595 11.23H17.0995H6.89953H5.53953C5.10953 11.23 4.76953 11.58 4.76953 12C4.76953 12.42 5.10953 12.77 5.53953 12.77H6.89953H17.0995H18.4595C18.8895 12.77 19.2295 12.42 19.2295 12C19.2295 11.58 18.8895 11.23 18.4595 11.23Z" fill="currentColor"/>
                <path d="M6.90039 13.94V14.27C6.90039 15.93 8.24039 17.27 9.90039 17.27H14.1004C15.7604 17.27 17.1004 15.93 17.1004 14.27V13.94C17.1004 13.82 17.0104 13.73 16.8904 13.73H7.11039C6.99039 13.73 6.90039 13.82 6.90039 13.94Z" fill="currentColor"/>
                <path d="M6.90039 10.06V9.72998C6.90039 8.06998 8.24039 6.72998 9.90039 6.72998H14.1004C15.7604 6.72998 17.1004 8.06998 17.1004 9.72998V10.06C17.1004 10.18 17.0104 10.27 16.8904 10.27H7.11039C6.99039 10.27 6.90039 10.18 6.90039 10.06Z" fill="currentColor"/>
            </svg>
        </div>
        <span class="nav-logo-text">Bet<span>Scan</span> AI</span>
    </a>
    <ul class="nav-links">
        <li><a href="#beranda">Home</a></li>
        <li><a href="#tentang">About</a></li>
        <li><a href="#teknologi">Technology</a></li>
        <li><a href="#mulai-deteksi" class="btn-nav-cta">Start Detection</a></li>
    </ul>
</nav>

{{-- SECTIONS --}}
@include('sections.beranda')
@include('sections.tentang')
@include('sections.teknologi')
@include('sections.deteksi')

{{-- FOOTER --}}
<footer style="background:var(--slate-900);color:rgba(255,255,255,.5);text-align:center;padding:2rem;font-size:.82rem;">
    <p>
        &copy; 2026 <strong style="color:rgba(255,255,255,.8);">BetScan AI</strong> — Adaptive Recommendation System for Online Gambling Content Detection<br>
        <span style="font-size:.75rem;opacity:.6;">Developed by Satria Abrar Sambarana Wira Pratama &bull; Politeknik Negeri Malang</span>
    </p>
</footer>

<div class="toast-container" id="toastContainer"></div>

{{-- JS GLOBAL --}}
<script>
// Navbar scroll effect
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 10);
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

// Toast helper — global, dipakai sections/deteksi.blade.php
function showToast(message, type = 'error') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
</script>

</body>
</html>