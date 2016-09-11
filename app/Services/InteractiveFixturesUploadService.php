<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 10/09/2016
 * Time: 15:32
 */

namespace App\Services;

use Illuminate\Http\UploadedFile;

use App\Models\UploadJob;
use App\Services\Interfaces\InteractiveUploadInterface;

class InteractiveFixturesUploadService implements InteractiveUploadInterface
{
    const UPLOAD_DIR = '/app/files/';

    public function createJob(UploadedFile $file)
    {
        $fixtureFile = $file->move(storage_path() . self::UPLOAD_DIR);

        $job = UploadJob::create([
            'file'   => $fixtureFile->getFilename(),
            'type'   => UploadJob::TYPE_FIXTURES,
            'status' => json_encode([]),
        ]);

        return $job->id;
    }
}