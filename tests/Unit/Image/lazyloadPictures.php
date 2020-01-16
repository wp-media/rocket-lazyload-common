<?php
/**
 * Unit tests for RocketLazyload\Image::lazyloadPictures method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Image;
use Brain\Monkey\Functions;

/**
 * Tests for the RocketLazyload\Image::lazyloadPictures method
 *
 * @covers RocketLazyload\Image::lazyloadPictures
 * @group Image
 */
class TestLazyloadPictures extends TestCase
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
     * Test should return same HTML when there is no picture element
     */
    public function testShouldReturnSameWhenNoPicture()
    {
        $noimage = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/noimage.html');

        $this->assertSame(
            $noimage,
            $this->image->lazyloadPictures($noimage, $noimage)
        );
    }

    /**
     * Test should return HTML with picture elements lazyloaded
     */
    public function testShouldReturnPicturesLazyloaded()
    {
        Functions\when('absint')->alias(function ($value) {
            return abs(intval($value));
        });

        $original = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/pictures.html');
        $expected = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/image/pictureslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadPictures($original, $original)
        );
    }
}
