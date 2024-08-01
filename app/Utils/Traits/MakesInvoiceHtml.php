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

namespace App\Utils\Traits;

use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;
use Throwable;

/**
 * Class MakesInvoiceHtml.
 */
trait MakesInvoiceHtml
{
    private function parseLabelsAndValues($labels, $values, $section): string
    {
        $section = strtr($section, $labels);
        $section = strtr($section, $values);

        return $section;
    }

    /**
     * Parses the blade file string and processes the template variables.
     *
     * @param string $string The Blade file string
     * @param array $data The array of template variables
     * @return string         The return HTML string
     * @deprecated // not needed!
     * @throws FatalThrowableError
     */
    public function renderView($string, $data = []): string
    {
        $data['__env'] = app(Factory::class);

        return Blade::render($string, $data); //potential fix for removing eval()

    }

    /*
     * Returns the base template we will be using.
     */
    public function getTemplate(string $template = 'plain')
    {
        return File::get(resource_path('views/email/template/'.$template.'.blade.php'));
    }

    public function getTemplatePath(string $template = 'plain')
    {
        return 'email.template.'.$template;
    }
}
