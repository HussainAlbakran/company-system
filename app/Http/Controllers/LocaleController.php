<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(string $locale, Request $request): RedirectResponse
    {
        if (in_array($locale, config('locales.supported', ['ar', 'en', 'ur']), true)) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->back();
    }
}

