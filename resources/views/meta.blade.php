@if(isset(pwa_settings()->status) && pwa_settings()->status == 1)
@php $manifest = pwa_settings()->data['manifest']; @endphp
<!-- Web application manifest -->
<link rel="manifest" href="{{ route('pwa.manifest') }}">
<!-- Theme color for chrome on android -->
<meta name="theme-color" content="{{ $manifest['theme_color'] }}">
<!-- Add to homescreen for chrome on android -->
<meta name="mobile-web-app-capable" content="{{ $manifest['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="application-name" content="{{ $manifest['short_name'] }}">
<link rel="icon" sizes="{{ array_key_last($manifest['icons']) }}" href="{{ last_icon_src() }}">
<!-- Add to homescreen for safari on ios -->
<meta name="apple-mobile-web-app-capable" content="{{ $manifest['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="apple-mobile-web-app-status-bar-style" content="{{  $manifest['status_bar'] }}">
<meta name="apple-mobile-web-app-title" content="{{ $manifest['short_name'] }}">
<link rel="apple-touch-icon" href="{{ last_icon_src() }}">
<!-- Splashes for safari on ios -->
<link href="{{ $manifest['splash']['640x1136'] }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['750x1334'] }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['1242x2208'] }}" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['1125x2436'] }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['828x1792'] }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['1242x2688'] }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['1536x2048'] }}" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['1668x2224'] }}" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['1668x2388'] }}" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ $manifest['splash']['2048x2732'] }}" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="{{ $manifest['background_color'] }}">
<meta name="msapplication-TileImage" content="{{ last_icon_src() }}">
<!-- Register serviceworker -->
<script src="{{ route('pwa.serviceworker.register') }}"></script>
@endif