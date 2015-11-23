<?php

namespace Omnipay\NABTransact\Tests\Lib;

trait fakerTrait
{
    protected static $faker = null;

    public static function setUpBeforeClass()
    {
        // Help generate fake content
        self::$faker = \Faker\Factory::create('en_AU');
    }
}