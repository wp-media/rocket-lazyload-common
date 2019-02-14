<?php
/**
 * Unit tests for get excluded patterns methods
 *
 * @package RocketLazyload\Tests\Unit\Image
 */

namespace RocketLazyload\Tests\Unit\Image;

use PHPUnit\Framework\TestCase;
use RocketLazyload\Image;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Tests the RocketLazyload\Image::getExcludedAttributes & RocketLazyload\Image::getExcludedSrc methods
 */
class TestExcluded extends TestCase
{
    /**
     * Instance of RocketLazyload\Image
     *
     * @var RocketLazyload\Image
     */
    private $image;

    /**
     * Do this before each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->image = new Image();
        Monkey\Setup();
    }

    /**
     * Do this after each test
     *
     * @return void
     */
    public function tearDown()
    {
        Monkey\TearDown();
    }

    /**
     * Test the returned array matches with the expected
     */
    public function testShouldReturnArrayExcludedAttributes()
    {
        $expected = [
            'data-src=',
            'data-no-lazy=',
            'data-lazy-original=',
            'data-lazy-src=',
            'data-lazysrc=',
            'data-lazyload=',
            'data-bgposition=',
            'data-envira-src=',
            'fullurl=',
            'lazy-slider-img=',
            'data-srcset=',
            'class="ls-l',
            'class="ls-bg',
        ];

        $this->assertSame(
            $expected,
            $this->image->getExcludedAttributes()
        );
    }

    /**
     * Test the returned array matches with the expected
     */
    public function testShouldReturnArrayExcludedSrc()
    {
        $expected = [
            '/wpcf7_captcha/',
            'timthumb.php?src',
        ];

        $this->assertSame(
            $expected,
            $this->image->getExcludedSrc()
        );
    }
}
