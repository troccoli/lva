<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 23/08/2016
 * Time: 19:33
 */

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;

use App\Services\InteractiveFixturesUploadService as FileUploadService;
use Illuminate\Http\Request;
use App\Models\Season;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class LoadController extends Controller
{
    /** @var FileUploadService */
    private $uploadService;

    /**
     * @inheritDoc
     */
    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }


    public function uploadFixtures()
    {
        return view('admin.data-management.load.fixtures', ['seasons' => Season::all()]);
    }

    public function startUploadFixtures(Request $request)
    {
        $this->validate($request, [
            'season_id'   => 'required|exists:seasons,id',
            'upload_file' => 'required|file|required_headers:Region,Code,Match,Home,Away,WUTime,StartTime,Discipline,Hall',
        ]);

        // Create upload job
        $jobId = $this->uploadService->createJob($request->file('upload_file'));

        // Redirect to status page (this will actually start the job)
        return Redirect::route('uploadStatus', ['job_id' => $jobId]);
    }

    public function processJob()
    {
        $jobId = Input::get('job_id', null);
        if (is_null($jobId) || (int)$jobId != $jobId || (int)$jobId <= 0) {
            return Redirect::route('uploadFixtures');
        }

        Artisan::call('lva:load:fixtures', ['job' => $jobId]);

        return view('admin.data-management.load.status');
    }
}