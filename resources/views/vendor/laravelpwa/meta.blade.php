@php
    $isTenant = tenancy()->initialized ?? false;
    if ($isTenant) {
        $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray();
        $tenantName = $settings['restaurant_name'] ?? 'Mi Restaurante';
        $tenantLogo = !empty($settings['logo']) ? Storage::url($settings['logo']) : '/images/icons/icon-192x192.png';
        $tenantColor = $settings['primary_color'] ?? '#f97316';
    }
@endphp

@if($isTenant)
    <!-- Web Application Manifest dinámico del tenant -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="{{ $tenantColor }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="{{ $tenantName }}">
    <link rel="icon" sizes="192x192" href="{{ $tenantLogo }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $tenantName }}">
    <link rel="apple-touch-icon" href="{{ $tenantLogo }}">
@else
    <!-- Web Application Manifest -->
    <link rel="manifest" href="{{ route('laravelpwa.manifest') }}">
    <meta name="theme-color" content="{{ $config['theme_color'] }}">
    <meta name="mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
    <meta name="application-name" content="{{ $config['short_name'] }}">
    <link rel="icon" sizes="{{ data_get(end($config['icons']), 'sizes') }}" href="{{ data_get(end($config['icons']), 'src') }}">
    <meta name="apple-mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="{{  $config['status_bar'] }}">
    <meta name="apple-mobile-web-app-title" content="{{ $config['short_name'] }}">
    <link rel="apple-touch-icon" href="{{ data_get(end($config['icons']), 'src') }}">
    <link href="{{ $config['splash']['640x1136'] }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['750x1334'] }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['1242x2208'] }}" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['1125x2436'] }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['828x1792'] }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['1242x2688'] }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['1536x2048'] }}" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['1668x2224'] }}" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['1668x2388'] }}" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ $config['splash']['2048x2732'] }}" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <meta name="msapplication-TileColor" content="{{ $config['background_color'] }}">
    <meta name="msapplication-TileImage" content="{{ data_get(end($config['icons']), 'src') }}">
@endif

<script type="text/javascript">
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/serviceworker.js', {
            scope: '.'
        }).then(function (registration) {
            console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    }
</script>
