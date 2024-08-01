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

namespace App\Transformers;

use App\Models\Client;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Utils\Traits\MakesHash;
use League\Fractal\Resource\Item;

/**
 * class TaskTransformer.
 */
class TaskTransformer extends EntityTransformer
{
    use MakesHash;

    protected array $defaultIncludes = [
        'documents',
        'project',
    ];

    /**
     * @var array
     */
    protected array $availableIncludes = [
        'client',
        'status',
        'project',
        'user',
        'invoice',
        'assigned_user',
    ];

    public function includeDocuments(Task $task)
    {
        $transformer = new DocumentTransformer($this->serializer);

        return $this->includeCollection($task->documents, $transformer, Document::class);
    }

    public function includeInvoice(Task $task): ?Item
    {
        $transformer = new InvoiceTransformer($this->serializer);

        if (!$task->invoice) {
            return null;
        }

        return $this->includeItem($task->invoice, $transformer, Invoice::class);
    }

    public function includeUser(Task $task): ?Item
    {
        $transformer = new UserTransformer($this->serializer);

        if (!$task->user) { //@phpstan-ignore-line
            return null;
        }

        return $this->includeItem($task->user, $transformer, User::class);
    }

    public function includeAssignedUser(Task $task): ?Item
    {
        $transformer = new UserTransformer($this->serializer);

        if (!$task->assigned_user) {
            return null;
        }

        return $this->includeItem($task->assigned_user, $transformer, User::class);
    }

    public function includeClient(Task $task): ?Item
    {
        $transformer = new ClientTransformer($this->serializer);

        if (!$task->client) {
            return null;
        }

        return $this->includeItem($task->client, $transformer, Client::class);
    }

    public function includeStatus(Task $task): ?Item
    {
        $transformer = new TaskStatusTransformer($this->serializer);

        if (!$task->status) {
            return null;
        }

        return $this->includeItem($task->status, $transformer, TaskStatus::class);
    }

    public function includeProject(Task $task): ?Item
    {
        $transformer = new ProjectTransformer($this->serializer);

        if ($task->project) {
            return $this->includeItem($task->project, $transformer, Project::class);
        }

        return null;
    }

    public function transform(Task $task)
    {
        return [
            'id' => (string) $this->encodePrimaryKey($task->id),
            'user_id' => (string) $this->encodePrimaryKey($task->user_id),
            'assigned_user_id' => (string) $this->encodePrimaryKey($task->assigned_user_id),
            'number' => (string) $task->number ?: '',
            'description' => (string) $task->description ?: '',
            'duration' => (int) $task->duration ?: 0,
            'rate' => (float) $task->rate ?: 0,
            'created_at' => (int) $task->created_at,
            'updated_at' => (int) $task->updated_at,
            'archived_at' => (int) $task->deleted_at,
            'invoice_id' => $this->encodePrimaryKey($task->invoice_id) ?: '',
            'client_id' => $this->encodePrimaryKey($task->client_id) ?: '',
            'project_id' => $this->encodePrimaryKey($task->project_id) ?: '',
            'is_deleted' => (bool) $task->is_deleted,
            'time_log' => $task->time_log ?: '',
            'is_running' => (bool) $task->is_running, //@deprecate
            'custom_value1' => $task->custom_value1 ?: '',
            'custom_value2' => $task->custom_value2 ?: '',
            'custom_value3' => $task->custom_value3 ?: '',
            'custom_value4' => $task->custom_value4 ?: '',
            'status_id' => $this->encodePrimaryKey($task->status_id) ?: '',
            'status_sort_order' => (int) $task->status_sort_order, //deprecated 5.0.34
            'is_date_based' => (bool) $task->is_date_based,
            'status_order' => is_null($task->status_order) ? null : (int) $task->status_order,
            'date' => $task->calculated_start_date ?: '',
        ];
    }
}
