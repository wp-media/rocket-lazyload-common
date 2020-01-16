<?php
/**
 * Unit tests for RocketLazyload\Image::lazyloadBackgroundImages method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Image;
use Brain\Monkey\Functions;

/**
 * Tests for the RocketLazyload\Image::lazyloadBackgroundImages method
 *
 * @covers RocketLazyload\Image::lazyloadBackgroundImages
 * @group Image
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
        $this->image = new Image();
    }

    /**
     * Test should return same HTML when there is no background images
     */
    public function testShouldReturnSameWhenNoBackgroundImage()
    {
        $noimage = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/noimage.html');

        $this->assertSame(
            $noimage,
            $this->image->lazyloadBackgroundImages($noimage, $noimage)
        );
    }

    /**
     * Test should return HTML with lazyloaded background images
     */
    public function testShouldReturnBackgroundImagesLazyloaded()
    {
        Functions\when('esc_attr')->returnArg();

        $original = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/bgimages.html');
        $expected = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/bgimageslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadBackgroundImages($original, $original)
        );
    }
}
