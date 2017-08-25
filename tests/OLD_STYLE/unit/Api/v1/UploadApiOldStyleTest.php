<?php

namespace Tests\Api\v1;

use LVA\Models\Team;
use LVA\Models\UploadJob;
use LVA\Models\UploadJobStatus;
use LVA\Models\Venue;
use LVA\Services\InteractiveFixturesUploadService;
use LVA\User;
use Prophecy\Argument;
use Tests\OldStyleTestCase;

/**
 * Class UploadApiTest
 *
 * @package Tests\Api\v1
 */
class UploadApiOldStyleTest extends OldStyleTestCase
{
    /**
     * @test
     */
    public function it_resumes_a_job()
    {
        $service = $this->prophesize(InteractiveFixturesUploadService::class);
        $service->processJob(Argument::any())->shouldBeCalled()->willReturn(null);
        $this->app->bind(InteractiveFixturesUploadService::class, function () use ($service) {
            return $service->reveal();
        });

        // Without an api token it redirects
        $this->get(route('resume-upload'))->seeStatusCode(302);

        /** @var User $user */
        $user = factory(User::class)->create();

        // Without a job it fails
        $this->get(route('resume-upload', ['api_token' => $user->api_token]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job parameter missing',
            ]);

        // Without a valid job it fails too
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => 1]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job not found',
            ]);

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED])->toArray())->save();
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job resumed',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS])->toArray())->save();
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be resumed',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA])->toArray())->save();
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job resumed',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR])->toArray())->save();
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be resumed',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT])->toArray())->save();
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job resumed',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS])->toArray())->save();
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be resumed',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR])->toArray())->save();
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be resumed',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE])->toArray())->save();
        $this->get(route('resume-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be resumed',
            ]);
    }

    /**
     * @test
     */
    public function it_abandons_a_job()
    {
        $service = $this->prophesize(InteractiveFixturesUploadService::class);
        $service->cleanUp(Argument::any())->shouldBeCalled()->willReturn(null);
        $this->app->bind(InteractiveFixturesUploadService::class, function () use ($service) {
            return $service->reveal();
        });

        // Without an api token it redirects
        $this->get(route('abandon-upload'))->seeStatusCode(302);

        /** @var User $user */
        $user = factory(User::class)->create();

        // Without a job it fails
        $this->get(route('abandon-upload', ['api_token' => $user->api_token]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job parameter missing',
            ]);

        // Without a valid job it fails too
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => 1]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job not found',
            ]);

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED])->toArray())->save();
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be abandoned',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS])->toArray())->save();
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be abandoned',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA])->toArray())->save();
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be abandoned',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR])->toArray())->save();
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be abandoned',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT])->toArray())->save();
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job abandoned',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS])->toArray())->save();
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be abandoned',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR])->toArray())->save();
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be abandoned',
            ]);

        $job->setStatus($this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE])->toArray())->save();
        $this->get(route('abandon-upload', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job cannot be abandoned',
            ]);
    }

    /**
     * @test
     */
    public function it_gets_the_job_status()
    {
        // Without an api token it redirects
        $this->get(route('upload-status'))->seeStatusCode(302);

        /** @var User $user */
        $user = factory(User::class)->create();

        // Without a job it fails
        $this->get(route('upload-status', ['api_token' => $user->api_token]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job parameter missing',
            ]);

        // Without a valid job it fails too
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => 1]))
            ->seeJsonContains([
                'Error'   => true,
                'Message' => 'Job not found',
            ]);

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $status->status_code = UploadJobStatus::STATUS_NOT_STARTED;
        $job->setStatus($status->toArray())->save();
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job found',
                'Status'  => $status->toApiArray(),
            ]);

        $status->status_code = UploadJobStatus::STATUS_VALIDATING_RECORDS;
        $job->setStatus($status->toArray())->save();
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job found',
                'Status'  => $status->toApiArray(),
            ]);

        $status->status_code = UploadJobStatus::STATUS_UNKNOWN_DATA;
        $job->setStatus($status->toArray())->save();
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job found',
                'Status'  => $status->toApiArray(),
            ]);

        $status->status_code = UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR;
        $job->setStatus($status->toArray())->save();
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job found',
                'Status'  => $status->toApiArray(),
            ]);

        $status->status_code = UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT;
        $job->setStatus($status->toArray())->save();
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job found',
                'Status'  => $status->toApiArray(),
            ]);

        $status->status_code = UploadJobStatus::STATUS_INSERTING_RECORDS;
        $job->setStatus($status->toArray())->save();
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job found',
                'Status'  => $status->toApiArray(),
            ]);

        $status->status_code = UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR;
        $job->setStatus($status->toArray())->save();
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job found',
                'Status'  => $status->toApiArray(),
            ]);

        $status->status_code = UploadJobStatus::STATUS_DONE;
        $job->setStatus($status->toArray())->save();
        $this->get(route('upload-status', ['api_token' => $user->api_token, 'job' => $job->getId()]))
            ->seeJsonContains([
                'Error'   => false,
                'Message' => 'Job found',
                'Status'  => $status->toApiArray(),
            ]);
    }

    /**
     * @test
     */
    public function it_maps_a_team()
    {
        // Missing api token
        $data = [];
        $this->post(route('loading-map-team'), $data)->seeStatusCode(302);

        /** @var User $user */
        $user = factory(User::class)->create();

        // Missing job field
        $data['api_token'] = $user->api_token;
        $this->json('POST', route('loading-map-team'), $data)
            ->seeJsonContains([
                'job' => ['The job field is required.'],
            ])
            ->dontSeeJson([
                'success' => true,
            ]);

        // Invalid job
        $data['job'] = 1;
        $this->json('POST', route('loading-map-team'), $data)
            ->seeJsonContains([
                'job' => ['The selected job is invalid.'],
            ])
            ->dontSeeJson([
                'success' => true,
            ]);

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        // Missing name field
        $data['job'] = $job->getId();
        $this->json('POST', route('loading-map-team'), $data)
            ->seeJsonContains([
                'name' => ['The name field is required.'],
            ])
            ->dontSeeJson([
                'success' => true,
            ]);

        $name = str_random();
        // Missing newName field
        $data['name'] = $name;
        $this->json('POST', route('loading-map-team'), $data)
            ->seeJsonContains([
                'newName' => ['The new name field is required.'],
            ])
            ->dontSeeJson([
                'success' => true,
            ]);

        /** @var Team $team */
        $team = factory(Team::class)->create();
        $data['newName'] = $team->getName();
        $this->json('POST', route('loading-map-team'), $data)
            ->seeJsonContains([
                'success' => true,
            ])
            ->seeInDatabase('mapped_teams', [
                'upload_job_id' => $job->getId(),
                'mapped_team'   => $name,
                'team_id'       => $team->getId(),
            ]);
    }

    /**
     * @test
     */
    public function it_maps_a_venue()
    {
        // Missing api token
        $data = [];
        $this->post(route('loading-map-venue'), $data)->seeStatusCode(302);

        /** @var User $user */
        $user = factory(User::class)->create();

        // Missing job field
        $data['api_token'] = $user->api_token;
        $this->json('POST', route('loading-map-venue'), $data)
            ->seeJsonContains([
                'job' => ['The job field is required.'],
            ])
            ->dontSeeJson([
                'success' => true,
            ]);

        // Invalid job
        $data['job'] = 1;
        $this->json('POST', route('loading-map-venue'), $data)
            ->seeJsonContains([
                'job' => ['The selected job is invalid.'],
            ])
            ->dontSeeJson([
                'success' => true,
            ]);

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        // Missing name field
        $data['job'] = $job->getId();
        $this->json('POST', route('loading-map-venue'), $data)
            ->seeJsonContains([
                'name' => ['The name field is required.'],
            ])
            ->dontSeeJson([
                'success' => true,
            ]);

        $name = str_random();
        // Missing newName field
        $data['name'] = $name;
        $this->json('POST', route('loading-map-venue'), $data)
            ->seeJsonContains([
                'newName' => ['The new name field is required.'],
            ])
            ->dontSeeJson([
                'success' => true,
            ]);

        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $data['newName'] = $venue->getName();
        $this->json('POST', route('loading-map-venue'), $data)
            ->seeJsonContains([
                'success' => true,
            ])
            ->seeInDatabase('mapped_venues', [
                'upload_job_id' => $job->getId(),
                'mapped_venue'  => $name,
                'venue_id'      => $venue->getId(),
            ]);
    }
}
