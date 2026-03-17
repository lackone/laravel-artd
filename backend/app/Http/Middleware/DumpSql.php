<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Log;

class DumpSql
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $action = $request->route()->getAction();

        Log::channel('dump_sql')->info(" ---------------------------- {$action['controller']} BEGIN ----------------------------");

        \Illuminate\Support\Facades\DB::listen(function ($query) {
            $bindings = $query->bindings;
            $sql = $query->sql;
            foreach ($bindings as $replace) {
                $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                $sql = preg_replace('/\?/', $value, $sql, 1);
            }
            Log::channel('dump_sql')->info($sql);
        });

        $response = $next($request);

        Log::channel('dump_sql')->info(" ---------------------------- {$action['controller']} END ----------------------------");

        return $response;
    }
}
