<?php

declare(strict_types=1);

namespace Challenge\Tests;

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;

// This defined constante is intentionally like this to help sort out a problem with `exit` in the
// code where WordPress does requires it, but it breaks the tests by not leeting you see the tests
// report.
define('CHALLENGE_UNIT_TESTING', 1); //phpcs:disable Inpsyde.CodeQuality.NoTopLevelDefine.Found
abstract class BaseTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
