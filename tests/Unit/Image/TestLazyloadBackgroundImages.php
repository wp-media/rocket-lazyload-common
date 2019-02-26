<?php
/**
 * Unit tests for RocketLazyload\Image::lazyloadBackgroundImages method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use RocketLazyload\Image;

/**
 * Tests for the RocketLazyload\Image::lazyloadBackgroundImages method
 *
 * @coversDefaultClass RocketLazyload\Image
 */
class TestLazyloadBackgroundImages extends TestCase
{
    /**
     * Instance of Image
     *
     * @var Image
     */
    private $image;

    /**
     * Do this before each test
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Monkey\setUp();
        $this->image = new Image();
    }

    /**
     * Do this after each test
     *
     * @return void
     */
    public function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * @covers ::lazyloadBackgroundImages
     * @author Remy Perona
     */
    public function testShouldReturnSameWhenNoBackgroundImage()
    {
        $noimage = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Image/noimage.html');

        $this->assertSame(
            $noimage,
            $this->image->lazyloadBackgroundImages($noimage, $noimage)
        );
    }

    /**
     * @covers ::lazyloadBackgroundImages
     * @author Remy Perona
     */
    public function testShouldReturnBackgroundImagesLazyloaded()
    {
        Functions\when('esc_attr')->returnArg();

        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Image/bgimages.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Image/bgimageslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadBackgroundImages($original, $original)
        );
    }
}
