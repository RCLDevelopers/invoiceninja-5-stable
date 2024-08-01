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

namespace App\DataMapper\Analytics;

use Turbo124\Beacon\ExampleMetric\GenericMixedMetric;

class EmailSuccess extends GenericMixedMetric
{
    /**
     * The type of Sample.
     *
     * Monotonically incrementing counter
     *
     *  - counter
     *
     * @var string
     */
    public $type = 'mixed_metric';

    /**
     * The name of the counter.
     * @var string
     */
    public $name = 'job.success.email';

    /**
     * The datetime of the counter measurement.
     *
     * date("Y-m-d H:i:s")
     *
     */
    public $datetime;

    /**
     * The Class failure name
     * set to 0.
     *
     * @var string
     */
    public $string_metric5 = 'stub';

    /**
     * The exception string
     * set to 0.
     *
     * @var string
     */
    public $string_metric6 = 'stub';

    /**
     * The counter
     * set to 1.
     *
     */
    public $int_metric1 = 1;

    /**
     * Company Key
     * @var string
     */
    public $string_metric7 = '';

    /**
     * Subject
     * @var string
     */
    public $string_metric8 = '';

    public function __construct($string_metric7 = '', $string_metric8 = '')
    {
        $this->string_metric7 = $string_metric7;
        $this->string_metric8 = $string_metric8;
    }
}
