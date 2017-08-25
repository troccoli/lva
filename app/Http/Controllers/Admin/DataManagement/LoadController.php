<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 23/08/2016
 * Time: 19:33
 */

namespace LVA\Http\Controllers\Admin\DataManagement;

use Illuminate\Http\Request;
use LVA\Http\Controllers\Controller;
use LVA\Models\Season;
use LVA\Models\UploadJob;
use LVA\Services\InteractiveFixturesUploadService as FileUploadService;

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
            'upload_file' => 'required|file|required_headers:Region,Code,Match,Home,Away,Date,WUTime,StartTime,Discipline,Hall',
        ]);

        // Create upload job
        $job = $this->uploadService->createJob($request->input('season_id'), $request->file('upload_file'));

        // Redirect to the status page
        return redirect()->route('uploadStatus', ['jobs_id' => $job->getId()]);
    }

    public function uploadStatus(UploadJob $uploadJob)
    {
        $uploadJob->save();

        return view('admin.data-management.load.status', ['job' => $uploadJob]);
    }
}