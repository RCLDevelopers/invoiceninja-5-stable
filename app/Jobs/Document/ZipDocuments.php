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

namespace App\Jobs\Document;

use App\Jobs\Mail\NinjaMailerJob;
use App\Jobs\Mail\NinjaMailerObject;
use App\Jobs\Util\UnlinkFile;
use App\Libraries\MultiDB;
use App\Mail\DownloadDocuments;
use App\Models\Company;
use App\Models\Document;
use App\Models\User;
use App\Utils\Ninja;
use App\Utils\Traits\MakesDates;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class ZipDocuments implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use MakesDates;

    public $document_ids;

    private $company;

    private $user;

    public $settings;

    public $tries = 1;

    /**
     * @param array $document_ids
     * @param Company $company
     * @param User $user
     * @deprecated confirm to be deleted
     * Create a new job instance.
     */
    public function __construct($document_ids, Company $company, User $user)
    {
        $this->document_ids = $document_ids;

        $this->company = $company;

        $this->user = $user;

        $this->settings = $company->settings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        MultiDB::setDb($this->company->db);

        App::setLocale($this->company->locale());
        App::forgetInstance('translator');
        $t = app('translator');
        $t->replace(Ninja::transformTranslations($this->company->settings));

        // create new zip object
        $zipFile = new \PhpZip\ZipFile();
        $file_name = date('Y-m-d').'_'.str_replace(' ', '_', trans('texts.documents')).'.zip';
        $path = $this->company->file_path();

        try {
            $documents = Document::query()->whereIn('id', $this->document_ids)->get();

            foreach ($documents as $document) {
                $zipFile->addFromString($this->buildFileName($document), $document->getFile());
            }

            Storage::put($path.$file_name, $zipFile->outputAsString());

            $nmo = new NinjaMailerObject();
            $nmo->mailable = new DownloadDocuments(Storage::url($path.$file_name), $this->company);
            $nmo->to_user = $this->user;
            $nmo->settings = $this->settings;
            $nmo->company = $this->company;

            NinjaMailerJob::dispatch($nmo);

            UnlinkFile::dispatch(config('filesystems.default'), $path.$file_name)->delay(now()->addHours(1));
        } catch (\PhpZip\Exception\ZipException $e) {
            nlog('could not make zip => '.$e->getMessage());
        } finally {
            $zipFile->close();
        }
    }

    private function buildFileName($document): string
    {
        $filename = $document->name;

        $date = $this->formatDate(Carbon::createFromTimestamp($document->created_at), 'Y-m-d');

        $number = '_';

        if (isset($document->documentable->number)) {
            $number = '_'.$document->documentable->number;
        }

        $entity = ctrans('texts.document');

        if (isset($document->documentable)) {
            $entity = $document->documentable->translate_entity();
        }

        return "{$date}_{$entity}{$number}_{$filename}";
    }
}
