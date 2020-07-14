<?php

use CodexShaper\PWA\Model\Setting;

if (!function_exists('pwa_asset')) {
    /**
     * Get asset from given path.
     *
     * @param  string  $path
     * @return \Illuminate\Http\Response
     */
    function pwa_asset($path)
    {
        return route('pwa.asset', ['path' => $path]);
    }
}

if (!function_exists('pwa_settings')) {
    /**
     * Get current domain's PWA settings.
     *
     * @return string
     */
    function pwa_settings()
    {
        return Setting::where('domain', '=', request()->getHttpHost())->first();
    }
}

if (!function_exists('pwa_meta')) {
    /**
     * Generate PWA meta, link and script tags.
     *
     * @return string
     */
    function pwa_meta()
    {
        echo view('pwa::meta')->render();
    }
}

if (!function_exists('last_icon_src')) {
    /**
     * Get last icon from manifest set.
     *
     * @return string
     */
    function last_icon_src()
    {
        $icons = pwa_settings()->data['manifest']['icons'];
        $lastIcon = end($icons);
        return $lastIcon['path'];
    }
}
