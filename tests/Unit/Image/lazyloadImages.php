<?php
/**
 * Unit tests for RocketLazyload\Image::lazyloadImages method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Image;
use Brain\Monkey\Functions;

/**
 * Tests for the RocketLazyload\Image::lazyloadImages method
 *
 * @covers RocketLazyload\Image::lazyloadImages
 * @group Image
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
        $this->image = new Image();
    }

    /**
     * Test should return same HTML when there is no images
     */
    public function testShouldReturnSameWhenNoImage()
    {
        $noimage = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/noimage.html');

        $this->assertSame(
            $noimage,
            $this->image->lazyloadImages($noimage, $noimage)
        );
    }

    /**
     * Test should return HTML with images lazyloaded
     */
    public function testShouldReturnImagesLazyloaded()
    {
        Functions\when('absint')->alias(function ($value) {
            return abs(intval($value));
        });

        $original = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/images.html');
        $expected = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/imageslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadImages($original, $original)
        );
    }
}
