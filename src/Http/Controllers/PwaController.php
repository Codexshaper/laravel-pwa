<?php

namespace CodexShaper\PWA\Http\Controllers;

use CodexShaper\PWA\Model\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class PwaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pwa = $this->getPwaInstance();

        return view('pwa::settings', compact('pwa'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!File::isDirectory(storage_path('app/public/pwa/images/icons'))) {
            Storage::makeDirectory('public/pwa/images/icons', 0777, true);
        }

        File::copyDirectory(
            config('pwa.icons_path', __DIR__ . '/../../../resources/icons'), 
            storage_path('app/public/pwa/images/icons')
        );

        $pwa = $this->getPwaInstance();

        if (!$pwa) {
            $pwa = new Setting();
        }

        $domain = request()->getHttpHost();
        $tenant_id = null;
        if (function_exists('tenant') && isset(tenant()->id)) {
            $tenant_id = tenant()->id;
        }

        $data = $this->getManifestData([
            'name'             => 'Demo App',
            'short_name'       => 'DA',
            'start_url'        => 'https://'.$domain.'/',
            'background_color' => '#ffffff',
            'theme_color'      => '#000000',
            'display'          => 'standalone',
        ]);

        $data['serviceworker'] = $this->generateServiceWorker();
        $data['register_serviceworker'] = $this->generateServiceWorkerRegister();

        $pwa->tenant_id = $tenant_id;
        $pwa->domain = $domain;
        $pwa->data = $data;
        $pwa->status = 1;
        $pwa->save();

        return redirect(route('pwa'))->with('success', 'Pwa created successfully.');
    }

    /**
     * Activate PWA for the current tenant.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function activate()
    {
        $pwa = $this->getPwaInstance();
        $pwa->status = 1;
        $pwa->save();

        return redirect(route('pwa'))->with('success', 'Pwa activated successfully.');
    }

    /**
     * Deactivate PWA for the current tenant.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function deactivate()
    {
        $pwa = $this->getPwaInstance();
        $pwa->status = 0;
        $pwa->save();

        return redirect(route('pwa'))->with('success', 'Pwa deactivated successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\PwaSetting $pwaSetting
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $Setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\PwaSetting $pwaSetting
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $Setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\PwaSetting          $pwaSetting
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Setting $Setting)
    {
        $request->validate([
            'name'             => 'required',
            'short_name'       => 'required',
            'start_url'        => 'required',
            'background_color' => 'required',
            'theme_color'      => 'required',
            'display'          => 'required',
        ]);
        if (isset($request->icons) && count($request->icons) > 0) {
            foreach ($request->icons as $key => $icon) {
                $destination_path = 'pwa/images/icons/icon-'.$key.'.png';
                Storage::disk('public')->putFileAs('', $icon, $destination_path);
            }
        }

        if (isset($request->splashes) && count($request->splashes)) {
            foreach ($request->splashes as $key => $splash) {
                $destination_path = 'pwa/images/icons/splash-'.$key.'.png';
                Storage::disk('public')->putFileAs('', $splash, $destination_path);
            }
        }

        $pwa = $this->getPwaInstance();

        if (!$pwa) {
            $pwa = new Setting();
        }

        $domain = request()->getHttpHost();
        $tenant_id = null;
        if (function_exists('tenant') && isset(tenant()->id)) {
            $tenant_id = tenant()->id;
        }

        $data = $this->getManifestData([
            'name'             => $request->name,
            'short_name'       => $request->short_name,
            'start_url'        => $request->start_url,
            'background_color' => $request->background_color,
            'theme_color'      => $request->theme_color,
            'display'          => $request->display,
        ]);

        $data['serviceworker'] = $this->generateServiceWorker();
        $data['register_serviceworker'] = $this->generateServiceWorkerRegister();

        $pwa->tenant_id = $tenant_id;
        $pwa->domain = $domain;
        $pwa->data = $data;
        $pwa->save();

        return redirect(route('pwa'))->with('success', 'Pwa settings updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\PwaSetting $pwaSetting
     *
     * @throws \Exception
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Setting $Setting)
    {
        try {
            if (File::isDirectory(storage_path('app/public/pwa'))) {
                File::deleteDirectory(storage_path('app/public/pwa'));
            }

            $pwa = $this->getPwaInstance();

            if ($pwa) {
                $pwa->delete();

                return redirect(route('pwa'))->with('success', 'Pwa deleted successfully.');
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Return manifest.josn content.
     *
     * @return \Illuminate\Http\Response
     */
    public function manifest()
    {
        $pwa = $this->getPwaInstance();
        $manifest = $pwa->data['manifest'];
        $manifestArr = static::prepareManifest($manifest);

        return response()->json($manifestArr);
    }

    /**
     * Display pwa offline resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function offline()
    {
        return view('pwa::offline');
    }

    /**
     * Prepare manifest data.
     *
     * @param array $manifest
     *
     * @return array
     */
    public static function prepareManifest($manifest)
    {
        $data = [
            'name'             => $manifest['name'],
            'short_name'       => $manifest['short_name'],
            'start_url'        => asset($manifest['start_url']),
            'display'          => $manifest['display'],
            'theme_color'      => $manifest['theme_color'],
            'background_color' => $manifest['background_color'],
            'orientation'      => $manifest['orientation'],
            'status_bar'       => $manifest['status_bar'],
            'splash'           => $manifest['splash'],
        ];

        foreach ($manifest['icons'] as $size => $file) {
            $fileInfo = pathinfo($file['path']);
            $data['icons'][] = [
                'src'     => $file['path'],
                'type'    => 'image/'.$fileInfo['extension'],
                'sizes'   => $size,
                'purpose' => $file['purpose'],
            ];
        }

        foreach ($manifest['shortcuts'] as $shortcut) {
            if (array_key_exists('icons', $shortcut)) {
                $fileInfo = pathinfo($shortcut['icons']['src']);
                $icon = [
                    'src'     => $shortcut['icons']['src'],
                    'type'    => 'image/'.$fileInfo['extension'],
                    'purpose' => $shortcut['icons']['purpose'],
                ];
            } else {
                $icon = [];
            }

            $data['shortcuts'][] = [
                'name'        => trans($shortcut['name']),
                'description' => trans($shortcut['description']),
                'url'         => $shortcut['url'],
                'icons'       => [
                    $icon,
                ],
            ];
        }

        foreach ($manifest['custom'] as $tag => $value) {
            $data[$tag] = $value;
        }

        return $data;
    }

    /**
     * Prepare manifest data from request for database.
     *
     * @param array $data
     *
     * @return array
     */
    protected function getManifestData($data)
    {
        return [
            'name'     => $data['name'],
            'manifest' => [
                'name'             => $data['name'],
                'short_name'       => $data['short_name'],
                'start_url'        => $data['start_url'],
                'background_color' => $data['background_color'],
                'theme_color'      => $data['theme_color'],
                'display'          => $data['display'],
                'orientation'      => 'any',
                'status_bar'       => 'black',
                'icons'            => [
                    '72x72' => [
                        'path'    => pwa_asset('images/icons/icon-72x72.png'),
                        'purpose' => 'any',
                    ],
                    '96x96' => [
                        'path'    => pwa_asset('images/icons/icon-96x96.png'),
                        'purpose' => 'any',
                    ],
                    '128x128' => [
                        'path'    => pwa_asset('images/icons/icon-128x128.png'),
                        'purpose' => 'any',
                    ],
                    '144x144' => [
                        'path'    => pwa_asset('images/icons/icon-144x144.png'),
                        'purpose' => 'any',
                    ],
                    '152x152' => [
                        'path'    => pwa_asset('images/icons/icon-152x152.png'),
                        'purpose' => 'any',
                    ],
                    '192x192' => [
                        'path'    => pwa_asset('images/icons/icon-192x192.png'),
                        'purpose' => 'any',
                    ],
                    '384x384' => [
                        'path'    => pwa_asset('images/icons/icon-384x384.png'),
                        'purpose' => 'any',
                    ],
                    '512x512' => [
                        'path'    => pwa_asset('images/icons/icon-512x512.png'),
                        'purpose' => 'any',
                    ],
                ],
                'splash' => [
                    '640x1136'  => pwa_asset('images/icons/splash-640x1136.png'),
                    '750x1334'  => pwa_asset('images/icons/splash-750x1334.png'),
                    '828x1792'  => pwa_asset('images/icons/splash-828x1792.png'),
                    '1125x2436' => pwa_asset('images/icons/splash-1125x2436.png'),
                    '1242x2208' => pwa_asset('images/icons/splash-1242x2208.png'),
                    '1242x2688' => pwa_asset('images/icons/splash-1242x2688.png'),
                    '1536x2048' => pwa_asset('images/icons/splash-1536x2048.png'),
                    '1668x2224' => pwa_asset('images/icons/splash-1668x2224.png'),
                    '1668x2388' => pwa_asset('images/icons/splash-1668x2388.png'),
                    '2048x2732' => pwa_asset('images/icons/splash-2048x2732.png'),
                ],
                'shortcuts' => [],
                'custom'    => [],
            ],
        ];
    }

    /**
     * Return serviceworker.js content.
     *
     * @return \Illuminate\Http\Response
     */
    public function serviceWorker()
    {
        $pwa = $this->getPwaInstance();

        if ($pwa) {
            $response = Response::make($pwa->data['serviceworker'], 200);
            $response->header('Content-Type', 'text/javascript');
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }
    }

    /**
     * Return serviceworker.js content.
     *
     * @return \Illuminate\Http\Response
     */
    public function registerServiceWorker()
    {
        $pwa = $this->getPwaInstance();

        if ($pwa) {
            $response = Response::make($pwa->data['register_serviceworker'], 200);
            $response->header('Content-Type', 'text/javascript');
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }
    }

    /**
     * Generate service worker.
     *
     * @return string
     */
    protected function generateServiceWorker()
    {
        $public_path = asset('/');
        $pwa_asset = pwa_asset('');
        $base_url = url('/');

        return <<<SERVICE_WORKER
        var staticCacheName = "pwa-v" + new Date().getTime();
        var filesToCache = [
            '$base_url/offline',
            '{$base_url}/css/app.css',
            '{$base_url}/js/app.js',
            '$pwa_asset/images/icons/icon-72x72.png',
            '$pwa_asset/images/icons/icon-96x96.png',
            '$pwa_asset/images/icons/icon-128x128.png',
            '$pwa_asset/images/icons/icon-144x144.png',
            '$pwa_asset/images/icons/icon-152x152.png',
            '$pwa_asset/images/icons/icon-192x192.png',
            '$pwa_asset/images/icons/icon-384x384.png',
            '$pwa_asset/images/icons/icon-512x512.png',
        ];

        // Cache on install
        self.addEventListener("install", event => {
            this.skipWaiting();
            event.waitUntil(
                caches.open(staticCacheName)
                    .then(cache => {
                        return cache.addAll(filesToCache);
                    })
            )
        });

        // Clear cache on activate
        self.addEventListener('activate', event => {
            event.waitUntil(
                caches.keys().then(cacheNames => {
                    return Promise.all(
                        cacheNames
                            .filter(cacheName => (cacheName.startsWith("pwa-")))
                            .filter(cacheName => (cacheName !== staticCacheName))
                            .map(cacheName => caches.delete(cacheName))
                    );
                })
            );
        });

        // Serve from Cache
        self.addEventListener("fetch", event => {
            event.respondWith(
                caches.match(event.request)
                    .then(response => {
                        return response || fetch(event.request);
                    })
                    .catch(() => {
                        return caches.match('offline');
                    })
            )
        });
SERVICE_WORKER;
    }

    /**
     * Generate service worker registerar.
     *
     * @return \App\PwaSetting
     */
    protected function generateServiceWorkerRegister()
    {
        $serviceworker_route = route('pwa.serviceworker');

        return <<<REGISTER_SERVICE_WORKER
            // Get serviceworker contents
            var serviceworker = "$serviceworker_route";
            // Initialize the service worker
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register(serviceworker, {
                    scope: '.'
                }).then(function (registration) {
                    // Registration was successful
                    console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
                }, function (err) {
                    // registration failed
                    console.log('Laravel PWA: ServiceWorker registration failed: ', err);
                });
            }
REGISTER_SERVICE_WORKER;
    }

    /**
     * Get PwaSetting instance.
     *
     * @return \App\PwaSetting
     */
    public function getPwaInstance()
    {
        return Setting::where('domain', '=', request()->getHttpHost())->first();
    }

    public function asset($path)
    {
        try {
            return response()->file(storage_path("app/public/pwa/$path"));
        } catch (\Throwable $th) {
            abort(404);
        }
    }
}
