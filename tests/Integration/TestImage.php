<?php
/**
 * Integration Tests for the Image class
 *
 * @package RocketLazyload
 */

namespace Rocketlazyload\Tests\Integration;

use WP_UnitTestCase;
use RocketLazyload\Image;

/**
 * Integration Tests for the Image class
 */
class TestImage extends WP_UnitTestCase
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
        $this->image = new Image();
    }

    /**
     * @covers ::lazyloadImages
     * @author Remy Perona
     */
    public function testShouldReturnSameWhenNoImage()
    {
        $noimage = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Image/noimage.html');

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
        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Image/images.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Image/imageslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadImages($original, $original)
        );
    }
}
