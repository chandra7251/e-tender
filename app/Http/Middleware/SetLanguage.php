<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    public function handle(Request $request, Closure $next): Response
    {
        // Priority: 1) query ?lang=en, 2) session, 3) Accept-Language header, 4) default
        $lang = $request->query('lang')
             ?? $request->session()->get('locale')
             ?? $this->parseAcceptLanguage($request)
             ?? config('app.locale', 'id');

        $supported = ['id', 'en'];
        if (!in_array($lang, $supported)) $lang = 'id';

        if ($request->has('lang')) {
            $request->session()->put('locale', $lang);
        }

        App::setLocale($lang);
        return $next($request);
    }

    private function parseAcceptLanguage(Request $request): ?string
    {
        $header = $request->header('Accept-Language', '');
        if (str_starts_with($header, 'en')) return 'en';
        if (str_starts_with($header, 'id')) return 'id';
        return null;
    }
}
