<?php


namespace Tests\Models;


use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Faker;
use Tests\TestCase;
use LVA\Models\UploadJobStatus;

class UploadJobStatusTest extends TestCase
{
    /** @var FactoryMuffin */
    protected static $fm;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // create a new factory muffin instance
        static::$fm = new FactoryMuffin();

        // load your model definitions
        static::$fm->loadFactories(database_path() . '/muffins');
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass()
    {
        static::$fm->deleteSaved();

        parent::tearDownAfterClass();
    }


    /**
     * @test
     */
    public function it_()
    {
    }
}
