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

namespace App\Http\Controllers;

use App\Exceptions\SystemError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProtectedDownloadController extends BaseController
{
    public function index(Request $request, string $hash)
    {
        /** @var string $hashed_path */
        $hashed_path = Cache::get($hash);

        if (!$hashed_path) {
            throw new SystemError('File no longer available', 404);
        }

        return response()->streamDownload(function () use ($hashed_path) {
            echo Storage::get($hashed_path);
        }, basename($hashed_path), []);

    }

}
