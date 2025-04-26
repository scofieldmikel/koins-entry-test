<?php

namespace App\Http\Middleware;

use App\Http\Traits\HasApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsProfileUpdated
{
    use HasApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->profile_image) {
            return $next($request);
        }

        return $this->badRequestResponse('Please update your profile image');
        // return $next($request);
    }
}
