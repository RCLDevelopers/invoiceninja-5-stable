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

class QueueSize extends GenericMixedMetric
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
    public $name = 'ninja.queue_size';

    /**
     * The datetime of the counter measurement.
     *
     * date("Y-m-d H:i:s")
     *
     * @var \DateTime
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
     * @var string
     */
    public $int_metric1 = 1;

    /**
     * Company Key
     * @var string
     */
    public $string_metric7 = '';

    public function __construct($int_metric1)
    {
        $this->int_metric1 = $int_metric1;
    }
}
