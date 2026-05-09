@php
    $primaryColor = \Illuminate\Support\Facades\DB::table('settings')
        ->where('key', 'primary_color')
        ->value('value') ?? '#f97316';
    $logo = \Illuminate\Support\Facades\DB::table('settings')
        ->where('key', 'logo')
        ->value('value');
    $logoUrl = $logo ? Storage::url($logo) : null;
@endphp

@if($logoUrl)
    <link rel="icon" type="image/png" href="{{ $logoUrl }}">
    <link rel="apple-touch-icon" href="{{ $logoUrl }}">
@endif

<style>
    :root {
        --color-primary: {{ $primaryColor }};
        --color-primary-hover: {{ $primaryColor }}dd;
        --color-primary-light: {{ $primaryColor }}20;
        --color-primary-border: {{ $primaryColor }}40;
    }

    /* Backgrounds */
    .bg-primary { background-color: var(--color-primary) !important; }
    .bg-primary-light { background-color: var(--color-primary-light) !important; }

    /* Botones principales */
    .btn-primary {
        background-color: var(--color-primary) !important;
        color: white !important;
    }
    .btn-primary:hover { background-color: var(--color-primary-hover) !important; }

    /* Textos */
    .text-primary { color: var(--color-primary) !important; }

    /* Borders */
    .border-primary { border-color: var(--color-primary) !important; }

    /* Focus rings */
    input:focus, select:focus, textarea:focus {
        --tw-ring-color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
    }

    /* Tailwind overrides para naranja */
    .bg-orange-500 { background-color: var(--color-primary) !important; }
    .bg-orange-400 { background-color: var(--color-primary) !important; }
    .bg-orange-600 { background-color: var(--color-primary-hover) !important; }
    .bg-orange-50 { background-color: var(--color-primary-light) !important; }
    .text-orange-500 { color: var(--color-primary) !important; }
    .text-orange-400 { color: var(--color-primary) !important; }
    .text-orange-600 { color: var(--color-primary) !important; }
    .text-orange-700 { color: var(--color-primary) !important; }
    .text-orange-100 { color: white !important; opacity: 0.8; }
    .border-orange-500 { border-color: var(--color-primary) !important; }
    .border-orange-400 { border-color: var(--color-primary) !important; }
    .border-orange-200 { border-color: var(--color-primary-border) !important; }
    .border-orange-300 { border-color: var(--color-primary-border) !important; }
    .ring-orange-400 { --tw-ring-color: var(--color-primary) !important; }
    .from-orange-500 { --tw-gradient-from: var(--color-primary) !important; }
    .to-orange-400 { --tw-gradient-to: var(--color-primary) !important; }
    .hover\:bg-orange-600:hover { background-color: var(--color-primary-hover) !important; }
    .hover\:bg-orange-100:hover { background-color: var(--color-primary-light) !important; }
    .accent-orange-500 { accent-color: var(--color-primary) !important; }
    .focus\:ring-orange-400:focus { --tw-ring-color: var(--color-primary) !important; }
</style>
