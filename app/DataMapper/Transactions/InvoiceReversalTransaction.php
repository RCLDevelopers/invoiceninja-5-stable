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

namespace App\DataMapper\Transactions;

use App\Models\TransactionEvent;

/**
 * InvoiceReversalTransaction.
 */
class InvoiceReversalTransaction extends BaseTransaction implements TransactionInterface
{
    public $event_id = TransactionEvent::INVOICE_REVERSED;
}
