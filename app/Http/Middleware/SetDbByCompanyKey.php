<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2024. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Http\Middleware;

use App\Libraries\MultiDB;
use Closure;
use Illuminate\Http\Request;
use stdClass;

class SetDbByCompanyKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $error = [
            'message' => 'Invalid Token',
            'errors' => new stdClass(),
        ];

        if ($request->header('X-API-COMPANY-KEY') && config('ninja.db.multi_db_enabled')) {
            if (! MultiDB::findAndSetDbByCompanyKey($request->header('X-API-COMPANY-KEY'))) {
                return response()->json($error, 403);
            }
        } elseif (! config('ninja.db.multi_db_enabled')) {
            return $next($request);
        } else {
            return response()->json($error, 403);
        }

        return $next($request);
    }
}
