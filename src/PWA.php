<?php

namespace CodexShaper\PWA;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class PWA
{
    public function routes()
    {
        require __DIR__.'/../routes/pwa.php';
    }

    /**
     * Load assests.
     *
     * @param string $path
     *
     * @return \Illuminate\Http\Response
     */
    public function assets($path)
    {
        $file = base_path(trim(config('pwa.resources_path'), '/').'/'.urldecode($path));

        if (File::exists($file)) {
            switch ($extension = pathinfo($file, PATHINFO_EXTENSION)) {
                case 'js':
                    $mimeType = 'text/javascript';
                    break;
                case 'css':
                    $mimeType = 'text/css';
                    break;
                default:
                    $mimeType = File::mimeType($file);
                    break;
            }

            $response = Response::make(File::get($file), 200);
            $response->header('Content-Type', $mimeType);
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }

        return response('', 404);
    }
}
