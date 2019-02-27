<?php
/**
 * Unit tests for RocketLazyload\Image::lazyloadPictures method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use RocketLazyload\Image;

/**
 * Tests for the RocketLazyload\Image::lazyloadPictures method
 *
 * @coversDefaultClass RocketLazyload\Image
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
     * @covers ::lazyloadPictures
     * @author Remy Perona
     */
    public function testShouldReturnSameWhenNoPicture()
    {
        $noimage = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/noimage.html');

        $this->assertSame(
            $noimage,
            $this->image->lazyloadPictures($noimage, $noimage)
        );
    }

    /**
     * @covers ::lazyloadPictures
     * @author Remy Perona
     */
    public function testShouldReturnPicturesLazyloaded()
    {
        Functions\when('absint')->alias(function ($value) {
            return abs(intval($value));
        });

        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/pictures.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/pictureslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadPictures($original, $original)
        );
    }
}
