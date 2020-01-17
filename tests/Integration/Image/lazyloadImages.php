<?php
/**
 * Integration Tests for the RocketLazyload\Iframe::lazyloadImages method
 *
 * @package Rocketlazyload\Tests\Integration
 */

namespace Rocketlazyload\Tests\Integration;

use RocketLazyload\Tests\Integration\TestCase;
use RocketLazyload\Image;

/**
 * Integration Tests for the RocketLazyload\Iframe::lazyloadImages method
 *
 * @covers RocketLazyload\Iframe::lazyloadImages
 * @group Image
 */
class TestImage extends TestCase
{
    /**
     * Image instance
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
     * Test should return same HTML when no images
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
        $original = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/images.html');
        $expected = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/imageslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadImages($original, $original)
        );
    }
}
