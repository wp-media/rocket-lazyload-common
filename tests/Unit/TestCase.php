<?php
/**
 * Test Case for all of the unit tests.
 *
 * @package RocketLazyload\Tests\Unit
 */

namespace RocketLazyload\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Brain\Monkey;

/**
 * Test Case for all of the unit tests.
 */
class TestCase extends PHPUnitTestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * Prepares the test environment before each test.
     */
    protected function setUp()
    {
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * Cleans up the test environment after each test.
     */
    protected function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
