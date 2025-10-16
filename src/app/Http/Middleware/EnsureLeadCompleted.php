<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use App\Models\Lead;

class EnsureLeadCompleted
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('lead_token');
        if (!$token || !Lead::where('lead_token', $token)->exists()) {
            return redirect()->to('/lead?redirect=' . urlencode($request->fullUrl()));
        }
        return $next($request);
    }
}
