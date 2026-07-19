<?php

namespace App\Support;

class CompanyBranding
{
    /** @var list<string> */
    private const LOGO_CANDIDATES = [
        'images/public/logo.svg',
        'images/public/logo.png',
        'images/public/logo.webp',
        'images/logo.png',
    ];

    public static function logoUrl(): ?string
    {
        foreach (self::LOGO_CANDIDATES as $path) {
            if (is_file(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }
}
