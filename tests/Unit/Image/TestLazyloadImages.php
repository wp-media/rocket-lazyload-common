<?php
/**
 * Unit tests for RocketLazyload\Image::lazyloadImages method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use RocketLazyload\Image;

/**
 * Tests for the RocketLazyload\Image::lazyloadImages method
 *
 * @coversDefaultClass RocketLazyload\Image
 */
class TestLazyloadImages extends TestCase
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
     * @covers ::lazyloadImages
     * @author Remy Perona
     */
    public function testShouldReturnSameWhenNoImage()
    {
        $noimage = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/noimage.html');

        $this->assertSame(
            $noimage,
            $this->image->lazyloadImages($noimage, $noimage)
        );
    }

    /**
     * @covers ::lazyloadImages
     * @author Remy Perona
     */
    public function testShouldReturnImagesLazyloaded()
    {
        Functions\when('absint')->alias(function ($value) {
            return abs(intval($value));
        });

        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/images.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/imageslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadImages($original, $original)
        );
    }
}
