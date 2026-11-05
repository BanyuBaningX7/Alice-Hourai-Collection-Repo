<meta charset="utf-8">
<script>try{var n=performance.getEntriesByType&&performance.getEntriesByType('navigation')[0];var r=(n&&n.type==='reload')||(performance.navigation&&performance.navigation.type===1);if(!r&&sessionStorage.getItem('aliceSeen'))document.documentElement.classList.add('no-intro');sessionStorage.removeItem('aliceSeen')}catch(e){}</script>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Pinyon+Script&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
{{-- Tailwind (Vite) bersifat opsional: tema utama ada di public/css/alice.css --}}
@if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
<link rel="stylesheet" href="{{ asset('css/alice.css') }}?v=3">
<link rel="stylesheet" href="{{ asset('css/alice-update.css') }}?v=3">