<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ApiTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return error('Token not provided', [], 401, 401);
            }

            $payload = JWTAuth::setToken($token)->getPayload();

            if (!$payload['id']) {
                return error('not found', [], 401, 401);
            }

            $request->offsetSet('admin_id', $payload['id']);

        } catch (TokenExpiredException $e) {
            return error('Token has expired', [], 401, 401);
        } catch (TokenInvalidException $e) {
            return error('Token is invalid', [], 401, 401);
        } catch (\Exception $e) {
            return error($e->getMessage(), [], 401, 401);
        }

        return $next($request);
    }
}
