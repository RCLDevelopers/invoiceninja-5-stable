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

namespace App\Http\Requests\Preview;

use App\Http\Requests\Request;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\RecurringInvoice;
use App\Utils\Traits\CleanLineItems;
use App\Utils\Traits\MakesHash;

class DesignPreviewRequest extends Request
{
    use MakesHash;
    use CleanLineItems;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user->can('create', Invoice::class) ||
               $user->can('create', Quote::class) ||
               $user->can('create', RecurringInvoice::class) ||
               $user->can('create', Credit::class) ||
               $user->can('create', PurchaseOrder::class);
    }

    public function rules()
    {
        $rules = [
            'entity_type' => 'bail|required|in:invoice,quote,credit,purchase_order,statement,payment_receipt,payment_refund,delivery_note',
            'settings_type' => 'bail|required|in:company,group,client',
            'settings' => 'sometimes',
            'group_id' => 'sometimes',
            'client_id' => 'sometimes',
            'design' => 'bail|sometimes|array',
        ];

        return $rules;
    }

    public function prepareForValidation()
    {
        $input = $this->all();

        $input = $this->decodePrimaryKeys($input);

        $input['line_items'] = isset($input['line_items']) ? $this->cleanItems($input['line_items']) : [];
        $input['amount'] = 0;
        $input['balance'] = 0;
        $input['number'] = ctrans('texts.live_preview').' #'.rand(0, 1000);

        $this->replace($input);
    }
}
