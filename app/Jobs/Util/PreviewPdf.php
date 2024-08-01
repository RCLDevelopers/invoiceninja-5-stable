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

namespace App\Jobs\Util;

use App\Models\Company;
use App\Utils\Traits\Pdf\PageNumbering;
use App\Utils\Traits\Pdf\PdfMaker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PreviewPdf implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use PdfMaker;
    use PageNumbering;

    /**
     * Create a new job instance.
     *
     * @param $design_string
     * @param Company $company
     */
    public function __construct(public string $design_string, public Company $company)
    {
    }

    public function handle()
    {
        $pdf = $this->makePdf(null, null, $this->design_string);

        $numbered_pdf = $this->pageNumbering($pdf, $this->company);

        if ($numbered_pdf) {
            $pdf = $numbered_pdf;
        }

        return $pdf;
    }
}
