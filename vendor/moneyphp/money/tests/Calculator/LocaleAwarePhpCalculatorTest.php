<?php

namespace Tests\Money\Calculator;

final class LocaleAwarePhpCalculatorTest extends PhpCalculatorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->setLocale(LC_ALL, 'ru_RU.UTF-8');
    }
}
