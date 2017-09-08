<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use LVA\Models\Division;
use LVA\Models\Team;
use LVA\Models\UploadJobStatus;
use LVA\Models\Venue;
use Tests\TestCase;

/**
 * Class UploadJobStatusTest
 *
 * @package Tests\Unit\Models
 */
class UploadJobStatusTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_with_a_factory()
    {
        $statusArray = $this->uploadJobTestFactory()->toArray();
        $status = UploadJobStatus::factory($statusArray);
        $this->assertEquals($statusArray, $status->toArray());
    }

    /**
     * @test
     */
    public function it_gets_the_status_message()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertEquals('Not started', $status->getStatusCodeMessage());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertEquals('Validating records', $status->getStatusCodeMessage());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertEquals('Unrecoverable error', $status->getStatusCodeMessage());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertEquals('Waiting for confirmation from user', $status->getStatusCodeMessage());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertEquals('Inserting records', $status->getStatusCodeMessage());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertEquals('Unrecoverable error', $status->getStatusCodeMessage());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertEquals('Unknown data', $status->getStatusCodeMessage());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertEquals('Done', $status->getStatusCodeMessage());

        $status = $this->uploadJobTestFactory(['status_code' => -1]);
        $this->assertEquals('Status code -1 not recognised', $status->getStatusCodeMessage());
    }


    /**
     * @test
     */
    public function it_loads_a_status_from_an_array()
    {
        $statusArray = [
            'status_code'     => UploadJobStatus::STATUS_VALIDATING_RECORDS,
            'total_lines'     => $this->faker->numberBetween(10, 200),
            'processed_lines' => $this->faker->numberBetween(10, 200),
            'total_rows'      => $this->faker->numberBetween(10, 200),
            'processed_rows'  => $this->faker->numberBetween(10, 200),
            'processing_line' => [
                'division'     => $this->faker->word,
                'match_number' => $this->faker->numberBetween(1, 20),
                'home_team'    => $this->faker->word,
                'away_team'    => $this->faker->word,
                'date'         => $this->faker->date('D, d/m/Y'),
                'warm_up_time' => $this->faker->date('H:i'),
                'start_time'   => $this->faker->date('H:i'),
                'venue'        => $this->faker->word,
            ],
            'unknowns'        => [
                \LVA\Models\UploadJobStatus::UNKNOWN_VENUE => [
                    ['value' => $this->faker->numberBetween(10, 200), 'text' => $this->faker->word],
                    ['value' => $this->faker->numberBetween(10, 200), 'text' => $this->faker->word],
                    ['value' => $this->faker->numberBetween(10, 200), 'text' => $this->faker->word],
                ],
            ],
            'errors'          => $this->faker->sentences(),
            'error_line'      => $this->faker->numberBetween(10, 200),
        ];

        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertNotEquals($statusArray, $status->toArray());

        $status->load($statusArray);
        $this->assertEquals($statusArray, $status->toArray());
    }

    /**
     * @test
     */
    public function it_is_an_array()
    {
        $statusArray = $this->uploadJobTestFactory()->toArray();

        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('status_code', $statusArray);
        $this->assertArrayHasKey('total_lines', $statusArray);
        $this->assertArrayHasKey('processed_lines', $statusArray);
        $this->assertArrayHasKey('total_rows', $statusArray);
        $this->assertArrayHasKey('processed_rows', $statusArray);
        $this->assertArrayHasKey('processing_line', $statusArray);
        $this->assertArrayHasKey('unknowns', $statusArray);
        $this->assertArrayHasKey('errors', $statusArray);
        $this->assertArrayHasKey('error_line', $statusArray);
    }

    /**
     * @test
     */
    public function it_is_an_array_for_an_api_response()
    {
        $statusArray = $this->uploadJobTestFactory()->toApiArray();
        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('StatusCode', $statusArray);
        $this->assertArrayHasKey('StatusMessage', $statusArray);
        $this->assertArrayNotHasKey('Progress', $statusArray);
        $this->assertArrayNotHasKey('Fixture', $statusArray);
        $this->assertArrayNotHasKey('Unknowns', $statusArray);
        $this->assertArrayNotHasKey('Errors', $statusArray);
        $this->assertArrayNotHasKey('ErrorLine', $statusArray);

        $statusArray = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS])->toApiArray();
        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('StatusCode', $statusArray);
        $this->assertArrayHasKey('StatusMessage', $statusArray);
        $this->assertArrayHasKey('Progress', $statusArray);
        $this->assertArrayNotHasKey('Fixture', $statusArray);
        $this->assertArrayNotHasKey('Unknowns', $statusArray);
        $this->assertArrayNotHasKey('Errors', $statusArray);
        $this->assertArrayNotHasKey('ErrorLine', $statusArray);

        $statusArray = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA])->toApiArray();
        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('StatusCode', $statusArray);
        $this->assertArrayHasKey('StatusMessage', $statusArray);
        $this->assertArrayHasKey('Progress', $statusArray);
        $this->assertArrayHasKey('Fixture', $statusArray);
        $this->assertArrayHasKey('Division', $statusArray['Fixture']);
        $this->assertArrayHasKey('MatchNumber', $statusArray['Fixture']);
        $this->assertArrayHasKey('HomeTeam', $statusArray['Fixture']);
        $this->assertArrayHasKey('AwayTeam', $statusArray['Fixture']);
        $this->assertArrayHasKey('Date', $statusArray['Fixture']);
        $this->assertArrayHasKey('WarmUpTime', $statusArray['Fixture']);
        $this->assertArrayHasKey('StartTime', $statusArray['Fixture']);
        $this->assertArrayHasKey('Venue', $statusArray['Fixture']);
        $this->assertArrayHasKey('Unknowns', $statusArray);
        $this->assertArrayNotHasKey('Errors', $statusArray);
        $this->assertArrayNotHasKey('ErrorLine', $statusArray);

        $statusArray = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR])->toApiArray();
        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('StatusCode', $statusArray);
        $this->assertArrayHasKey('StatusMessage', $statusArray);
        $this->assertArrayHasKey('Progress', $statusArray);
        $this->assertArrayNotHasKey('Fixture', $statusArray);
        $this->assertArrayNotHasKey('Unknowns', $statusArray);
        $this->assertArrayHasKey('Errors', $statusArray);
        $this->assertInternalType('array', $statusArray['Errors']);
        $this->assertArrayHasKey('ErrorLine', $statusArray);

        $statusArray = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT])->toApiArray();
        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('StatusCode', $statusArray);
        $this->assertArrayHasKey('StatusMessage', $statusArray);
        $this->assertArrayNotHasKey('Progress', $statusArray);
        $this->assertArrayNotHasKey('Fixture', $statusArray);
        $this->assertArrayNotHasKey('Unknowns', $statusArray);
        $this->assertArrayNotHasKey('Errors', $statusArray);
        $this->assertArrayNotHasKey('ErrorLine', $statusArray);

        $statusArray = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS])->toApiArray();
        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('StatusCode', $statusArray);
        $this->assertArrayHasKey('StatusMessage', $statusArray);
        $this->assertArrayHasKey('Progress', $statusArray);
        $this->assertArrayNotHasKey('Fixture', $statusArray);
        $this->assertArrayNotHasKey('Unknowns', $statusArray);
        $this->assertArrayNotHasKey('Errors', $statusArray);
        $this->assertArrayNotHasKey('ErrorLine', $statusArray);

        $statusArray = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR])->toApiArray();
        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('StatusCode', $statusArray);
        $this->assertArrayHasKey('StatusMessage', $statusArray);
        $this->assertArrayHasKey('Progress', $statusArray);
        $this->assertArrayNotHasKey('Fixture', $statusArray);
        $this->assertArrayNotHasKey('Unknowns', $statusArray);
        $this->assertArrayHasKey('Errors', $statusArray);
        $this->assertInternalType('array', $statusArray['Errors']);
        $this->assertArrayHasKey('ErrorLine', $statusArray);

        $statusArray = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE])->toApiArray();
        $this->assertInternalType('array', $statusArray);
        $this->assertArrayHasKey('StatusCode', $statusArray);
        $this->assertArrayHasKey('StatusMessage', $statusArray);
        $this->assertArrayNotHasKey('Progress', $statusArray);
        $this->assertArrayNotHasKey('Fixture', $statusArray);
        $this->assertArrayNotHasKey('Unknowns', $statusArray);
        $this->assertArrayNotHasKey('Errors', $statusArray);
        $this->assertArrayNotHasKey('ErrorLine', $statusArray);
    }

    /**
     * @test
     */
    public function it_checks_if_it_has_started()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertFalse($status->hasStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertTrue($status->hasStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertTrue($status->hasStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertTrue($status->hasStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertTrue($status->hasStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertTrue($status->hasStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertTrue($status->hasStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertTrue($status->hasStarted());
    }

    /**
     * @test
     */
    public function it_checks_if_it_has_not_started_yet()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertTrue($status->hasNotStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertFalse($status->hasNotStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertFalse($status->hasNotStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertFalse($status->hasNotStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertFalse($status->hasNotStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertFalse($status->hasNotStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertFalse($status->hasNotStarted());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertFalse($status->hasNotStarted());
    }

    /**
     * @test
     */
    public function it_checks_if_it_is_validating()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertFalse($status->isValidating());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertTrue($status->isValidating());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertFalse($status->isValidating());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertFalse($status->isValidating());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertFalse($status->isValidating());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertFalse($status->isValidating());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertFalse($status->isValidating());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertFalse($status->isValidating());
    }

    /**
     * @test
     */
    public function it_checks_if_it_is_waiting_for_user_confirmation()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertFalse($status->isWaitingConfirmation());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertFalse($status->isWaitingConfirmation());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertFalse($status->isWaitingConfirmation());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertFalse($status->isWaitingConfirmation());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertTrue($status->isWaitingConfirmation());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertFalse($status->isWaitingConfirmation());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertFalse($status->isWaitingConfirmation());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertFalse($status->isWaitingConfirmation());
    }

    /**
     * @test
     */
    public function it_checks_if_it_is_inserting()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertFalse($status->isInserting());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertFalse($status->isInserting());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertFalse($status->isInserting());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertFalse($status->isInserting());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertFalse($status->isInserting());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertTrue($status->isInserting());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertFalse($status->isInserting());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertFalse($status->isInserting());
    }

    /**
     * @test
     */
    public function it_checks_if_it_is_done()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertFalse($status->isDone());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertFalse($status->isDone());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertFalse($status->isDone());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertFalse($status->isDone());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertFalse($status->isDone());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertFalse($status->isDone());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertFalse($status->isDone());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertTrue($status->isDone());
    }

    /**
     * @test
     */
    public function it_checks_if_it_has_unknownd_data()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertFalse($status->hasUnknownData());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertFalse($status->hasUnknownData());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertTrue($status->hasUnknownData());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertFalse($status->hasUnknownData());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertFalse($status->hasUnknownData());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertFalse($status->hasUnknownData());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertFalse($status->hasUnknownData());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertFalse($status->hasUnknownData());
    }

    /**
     * @test
     */
    public function it_checks_if_it_can_resume()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertTrue($status->canResume());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertFalse($status->canResume());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertTrue($status->canResume());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertFalse($status->canResume());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertTrue($status->canResume());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertFalse($status->canResume());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertFalse($status->canResume());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertFalse($status->canResume());
    }

    /**
     * @test
     */
    public function it_check_if_it_is_in_the_middle_of_processing_some_data()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertFalse($status->isWorking());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertTrue($status->isWorking());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertFalse($status->isWorking());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertFalse($status->isWorking());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertFalse($status->isWorking());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertTrue($status->isWorking());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertFalse($status->isWorking());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertFalse($status->isWorking());
    }

    /**
     * @test
     */
    public function it_gets_the_unknown_data()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->unknowns, $status->getUnknowns());
    }

    /**
     * @test
     */
    public function it_adds_a_new_unknown_data()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $unknowns = [
            UploadJobStatus::UNKNOWN_HOME_TEAM => factory(Team::class)->times(3)->make(),
            UploadJobStatus::UNKNOWN_AWAY_TEAM => factory(Team::class)->times(3)->make(),
            UploadJobStatus::UNKNOWN_VENUE     => factory(Team::class)->times(3)->make(),
        ];
        $status->setUnknown(UploadJobStatus::UNKNOWN_HOME_TEAM, $unknowns[UploadJobStatus::UNKNOWN_HOME_TEAM]);
        $status->setUnknown(UploadJobStatus::UNKNOWN_AWAY_TEAM, $unknowns[UploadJobStatus::UNKNOWN_AWAY_TEAM]);
        $status->setUnknown(UploadJobStatus::UNKNOWN_VENUE, $unknowns[UploadJobStatus::UNKNOWN_VENUE]);

        $this->assertEquals($unknowns, $status->getUnknowns());
    }

    /**
     * @test
     */
    public function it_checks_if_it_has_errors()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertFalse($status->hasErrors());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertFalse($status->hasErrors());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $this->assertFalse($status->hasErrors());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $this->assertTrue($status->hasErrors());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $this->assertFalse($status->hasErrors());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertFalse($status->hasErrors());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $this->assertTrue($status->hasErrors());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertFalse($status->hasErrors());
    }

    /**
     * @test
     */
    public function it_gets_the_errors()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->errors, $status->getErrors());
    }

    /**
     * @test
     */
    public function it_sets_validation_errors()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $errors = $this->faker->unique()->sentences;
        $status->setValidationErrors($errors, 0);

        $this->assertEquals($errors, $status->getErrors());
    }

    /**
     * @test
     */
    public function it_sets_a_inserting_error()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $error = $this->faker->unique()->sentence;
        $status->setInsertingError($error);

        $this->assertEquals([$error], $status->getErrors());
    }

    /**
     * @test
     */
    public function it_gets_the_line_where_the_error_occured()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->error_line, $status->getErrorLine());
    }

    /**
     * @test
     */
    public function it_gets_the_status_code()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->status_code, $status->getStatusCode());
    }

    /**
     * @test
     */
    public function it_gets_the_total_number_of_lines()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->total_lines, $status->getTotalLines());
    }

    /**
     * @test
     */
    public function it_sets_the_total_number_of_lines()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $lines = $this->faker->numberBetween(10, 200);

        $status->setTotalLines($lines);
        $this->assertEquals($lines, $status->getTotalLines());
    }

    /**
     * @test
     */
    public function it_gets_the_number_of_lines_that_has_been_processed_so_far()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processed_lines, $status->getProcessedLines());
    }

    /**
     * @test
     */
    public function it_sets_the_number_of_lines_that_has_been_processed_so_far()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $lines = $this->faker->numberBetween(10, 200);

        $status->setProcessedLines($lines);
        $this->assertEquals($lines, $status->getProcessedLines());
    }

    /**
     * @test
     */
    public function it_gets_the_total_number_of_rows_to_insert_into_the_db()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->total_rows, $status->getTotalRows());
    }

    /**
     * @test
     */
    public function it_sets_the_total_number_of_rows_to_insert_into_the_db()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $lines = $this->faker->numberBetween(10, 200);

        $status->setTotalRows($lines);
        $this->assertEquals($lines, $status->getTotalRows());
    }

    /**
     * @test
     */
    public function it_gets_the_number_of_rows_to_insert_into_the_db_that_has_been_processed_so_far()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processed_rows, $status->getProcessedRows());
    }

    /**
     * @test
     */
    public function it_sets_the_number_of_rows_to_insert_into_the_db_that_has_been_processed_so_far()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $lines = $this->faker->numberBetween(10, 200);

        $status->setProcessedRows($lines);
        $this->assertEquals($lines, $status->getProcessedRows());
    }

    /**
     * @test
     */
    public function it_gets_the_division_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processing_line['division'], $status->getProcessingLineDivision());

        $status = $this->uploadJobTestFactory(['processing_line' => []]);
        $this->assertEmpty($status->getProcessingLineDivision());
    }

    /**
     * @test
     */
    public function it_sets_the_division_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $division = factory(Division::class)->make()->getName();

        $status->setProcessingLineDivision($division);
        $this->assertEquals($division, $status->getProcessingLineDivision());
    }

    /**
     * @test
     */
    public function it_gets_the_match_number_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processing_line['match_number'], $status->getProcessingLineMatchNumber());

        $status = $this->uploadJobTestFactory(['processing_line' => []]);
        $this->assertEmpty($status->getProcessingLineMatchNumber());
    }

    /**
     * @test
     */
    public function it_sets_the_match_number_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $matchNumber = $this->faker->numberBetween(1, 100);

        $status->setProcessingLineMatchNumber($matchNumber);
        $this->assertEquals($matchNumber, $status->getProcessingLineMatchNumber());
    }

    /**
     * @test
     */
    public function it_gets_the_home_team_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processing_line['home_team'], $status->getProcessingLineHomeTeam());

        $status = $this->uploadJobTestFactory(['processing_line' => []]);
        $this->assertEmpty($status->getProcessingLineHomeTeam());
    }

    /**
     * @test
     */
    public function it_sets_the_home_team_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $team = factory(Team::class)->make()->getName();

        $status->setProcessingLineHomeTeam($team);
        $this->assertEquals($team, $status->getProcessingLineHomeTeam());
    }

    /**
     * @test
     */
    public function it_gets_the_away_team_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processing_line['away_team'], $status->getProcessingLineAwayTeam());

        $status = $this->uploadJobTestFactory(['processing_line' => []]);
        $this->assertEmpty($status->getProcessingLineAwayTeam());
    }

    /**
     * @test
     */
    public function it_sets_the_away_team_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $team = factory(Team::class)->make()->getName();

        $status->setProcessingLineAwayTeam($team);
        $this->assertEquals($team, $status->getProcessingLineAwayTeam());
    }

    /**
     * @test
     */
    public function it_gets_the_match_date_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processing_line['date'], $status->getProcessingLineDate());

        $status = $this->uploadJobTestFactory(['processing_line' => []]);
        $this->assertEmpty($status->getProcessingLineDate());
    }

    /**
     * @test
     */
    public function it_sets_the_match_date_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $time = Carbon::now();

        $status->setProcessingLineDate($time->format('d/m/Y'));
        $this->assertEquals($time->format('D, d/m/Y'), $status->getProcessingLineDate());
    }

    /**
     * @test
     */
    public function it_gets_the_warm_up_time_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processing_line['warm_up_time'], $status->getProcessingLineWarmUpTime());

        $status = $this->uploadJobTestFactory(['processing_line' => []]);
        $this->assertEmpty($status->getProcessingLineWarmUpTime());
    }

    /**
     * @test
     */
    public function it_sets_the_warm_up_time_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $time = Carbon::now();

        $status->setProcessingLineWarmUpTime($time->format('H:i:s'));
        $this->assertEquals($time->format('H:i'), $status->getProcessingLineWarmUpTime());
    }

    /**
     * @test
     */
    public function it_gets_the_start_time_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processing_line['start_time'], $status->getProcessingLineStartTime());

        $status = $this->uploadJobTestFactory(['processing_line' => []]);
        $this->assertEmpty($status->getProcessingLineStartTime());
    }

    /**
     * @test
     */
    public function it_sets_the_start_time_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $time = Carbon::now();

        $status->setProcessingLineStartTime($time->format('H:i:s'));
        $this->assertEquals($time->format('H:i'), $status->getProcessingLineStartTime());
    }

    /**
     * @test
     */
    public function it_gets_the_venue_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $this->assertEquals($status->processing_line['venue'], $status->getProcessingLineVenue());

        $status = $this->uploadJobTestFactory(['processing_line' => []]);
        $this->assertEmpty($status->getProcessingLineVenue());
    }

    /**
     * @test
     */
    public function it_sets_the_venue_for_the_current_line()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        $venue = factory(Venue::class)->make()->getName();

        $status->setProcessingLineVenue($venue);
        $this->assertEquals($venue, $status->getProcessingLineVenue());
    }

    /**
     * @test
     */
    public function it_can_move_forward()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $this->assertEquals(UploadJobStatus::STATUS_VALIDATING_RECORDS, $status->moveForward()->getStatusCode());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $this->assertEquals(UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT, $status->moveForward()->getStatusCode());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $this->assertEquals(UploadJobStatus::STATUS_DONE, $status->moveForward()->getStatusCode());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $this->assertEquals(UploadJobStatus::STATUS_DONE, $status->moveForward()->getStatusCode());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_cannot_move_forward_from_unknown_data()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $status->moveForward();
        $this->expectExceptionMessage("Invalid status code " . UploadJobStatus::STATUS_UNKNOWN_DATA . ".");
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_cannot_move_forward_from_an_unrecoverable_validation_error()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $status->moveForward();
        $this->expectExceptionMessage("Invalid status code " . UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR . ".");
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_cannot_move_forward_from_an_unrecoverable_insert_error()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $status->moveForward();
        $this->expectExceptionMessage("Invalid status code " . UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR . ".");
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_cannot_move_forward_from_waiting_for_user_confirmation()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $status->moveForward();
        $this->expectExceptionMessage("Invalid status code " . UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT . ".");
    }

    /**
     * @test
     */
    public function it_can_resume()
    {
        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_NOT_STARTED]);
        $prevStatus = clone $status;
        $status->resume();
        $this->assertEquals($prevStatus, $status);

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_VALIDATING_RECORDS]);
        $prevStatus = clone $status;
        $status->resume();
        $this->assertEquals($prevStatus, $status);

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA]);
        $status->resume();
        $this->assertEquals(UploadJobStatus::STATUS_VALIDATING_RECORDS, $status->getStatusCode());
        $this->assertEmpty($status->getUnknowns());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_VALIDATION_ERROR]);
        $prevStatus = clone $status;
        $status->resume();
        $this->assertEquals($prevStatus, $status);

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_WAITING_CONFIRMATION_TO_INSERT]);
        $status->resume();
        $this->assertEquals(UploadJobStatus::STATUS_INSERTING_RECORDS, $status->getStatusCode());

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_INSERTING_RECORDS]);
        $prevStatus = clone $status;
        $status->resume();
        $this->assertEquals($prevStatus, $status);

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_UNRECOVERABLE_INSERT_ERROR]);
        $prevStatus = clone $status;
        $status->resume();
        $this->assertEquals($prevStatus, $status);

        $status = $this->uploadJobTestFactory(['status_code' => UploadJobStatus::STATUS_DONE]);
        $prevStatus = clone $status;
        $status->resume();
        $this->assertEquals($prevStatus, $status);
    }
}
