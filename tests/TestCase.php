<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use LVA\Models\UploadJobStatus;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    protected function setUpTraits()
    {
        parent::setUpTraits();

        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[DatabaseTransactions::class])) {
            $database = $this->app->make('db');
            $this->beforeApplicationDestroyed(function () use ($database) {
                foreach ($this->connectionsToTransact() as $name) {
                    $database->connection($name)->disconnect();
                }
            });
        }
    }

    /**
     * @param array $overrides
     *
     * @return UploadJobStatus
     */
    protected function uploadJobTestFactory($overrides = [])
    {
        $totalLines = $this->faker->numberBetween(10, 200);
        $totalRows = $this->faker->numberBetween($totalLines, $totalLines * 2);

        /** @var \LVA\Models\Venue[] $mapping */
        $mapping = factory(\LVA\Models\Venue::class)->times(3)->make();

        $defaults = [
            "status_code"     => \LVA\Models\UploadJobStatus::STATUS_NOT_STARTED,
            "total_lines"     => $totalLines,
            "processed_lines" => $this->faker->numberBetween(0, $totalLines),
            "total_rows"      => $totalRows,
            "processed_rows"  => $this->faker->numberBetween(0, $totalRows),
            'processing_line' => [
                'division'     => factory(\LVA\Models\Division::class)->make()->getName(),
                'match_number' => $this->faker->numberBetween(1, 20),
                'home_team'    => factory(\LVA\Models\Team::class)->make()->getName(),
                'away_team'    => factory(\LVA\Models\Team::class)->make()->getName(),
                'date'         => $this->faker->date('D, d/m/Y'),
                'warm_up_time' => $this->faker->date('H:i'),
                'start_time'   => $this->faker->date('H:i'),
                'venue'        => factory(\LVA\Models\Venue::class)->make()->getName(),
            ],
            'unknowns'        => [
                \LVA\Models\UploadJobStatus::UNKNOWN_VENUE => [
                    ['value' => $mapping[0]->getId(), 'text' => $mapping[0]->getName()],
                    ['value' => $mapping[1]->getId(), 'text' => $mapping[1]->getName()],
                    ['value' => $mapping[2]->getId(), 'text' => $mapping[2]->getName()],
                ],
            ],
            "errors"          => $this->faker->unique()->sentences(),
            "error_line"      => $this->faker->numberBetween(1, $totalLines - 1),
        ];

        return UploadJobStatus::factory(array_merge($defaults, $overrides));
    }
}