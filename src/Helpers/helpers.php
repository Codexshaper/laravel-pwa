<?php

use CodexShaper\PWA\Model\Setting;

if (!function_exists('pwa_asset')) {
    function pwa_asset($path)
    {
        return route('pwa.asset', ['path' => $path]);
    }
}

if (!function_exists('pwa_settings')) {
    function pwa_settings()
    {
        return Setting::where('domain', '=', request()->getHttpHost())->first();
    }
}

if (!function_exists('pwa_meta')) {
    function pwa_meta()
    {
        $pwa = pwa_settings();

        echo view('pwa::meta', compact('pwa'))->render();
    }
}

if (!function_exists('last_icon_src')) {
    function last_icon_src()
    {
        $pwa = pwa_settings();
        $icons = $pwa->data['manifest']['icons'];
        // var_dump($icons);
        $lastIcon = end($icons);

        return $lastIcon['path'];
    }
}
