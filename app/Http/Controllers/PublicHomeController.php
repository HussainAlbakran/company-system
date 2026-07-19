<?php

namespace App\Http\Controllers;

use App\Support\CompanyBranding;
use Illuminate\Contracts\View\View;

class PublicHomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'logoUrl' => CompanyBranding::logoUrl(),
            'heroBgUrl' => $this->publicAssetUrl(['images/public/hero-bg.webp', 'images/public/hero-bg.jpg', 'images/public/hero-bg.png']),
        ]);
    }

    /**
     * @param  list<string>  $relativePaths  Paths under public/, first existing wins
     */
    private function publicAssetUrl(array $relativePaths): ?string
    {
        foreach ($relativePaths as $path) {
            if (is_string($path) && $path !== '' && file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }
}
