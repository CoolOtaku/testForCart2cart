<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function index()
    {
        return view('shorten');
    }

    public function shorten(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url'
        ]);

        $url = Url::where('original_url', $request->original_url)->first();

        if (!$url) {
            $shortUrl = substr(md5($request->original_url . microtime()), 0, 6);

            $url = Url::create([
                'original_url' => $request->original_url,
                'short_url' => $shortUrl
            ]);
        }

        return redirect()->back()->with('short_url', url($url->short_url));
    }

    public function redirect($shortUrl)
    {
        // Знайдемо оригінальний URL за його скороченим варіантом
        $url = Url::where('short_url', $shortUrl)->firstOrFail();

        // Перенаправляємо користувача на оригінальний URL
        return redirect()->away($url->original_url);
    }
}
