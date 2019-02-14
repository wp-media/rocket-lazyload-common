<?php
/**
 * Unit tests for RocketLazyload\Image::getPlaceholder method
 *
 * @package RocketLazyload\Tests\Unit\Image
 */

namespace RocketLazyload\Tests\Unit\Image;

use PHPUnit\Framework\TestCase;
use RocketLazyload\Image;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Tests for the RocketLazyload\Image::getPlaceholder method
 */
class TestGetPlaceholder extends TestCase
{
    /**
     * Instance of RocketLazyload\Image
     *
     * @var RocketLazyload\Image
     */
    private $image;

   
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
     * Test the method returns the placeholder with a default value of width & height at 1
     */
    public function testShouldReturnSVGPlaceholderWhenNoArguments()
    {
        Functions\when('absint')->alias(function ($value) {
            return abs(intval($value));
        });

        $placeholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E";

        $this->assertSame(
            $placeholder,
            $this->image->getPlaceholder()
        );
    }

    /**
     * Test the method returns the placeholder with correct values for width & height
     *
     * @dataProvider widthHeightProvider
     *
     * @param int    $width    Width of the placeholder.
     * @param int    $height   Height of the placeholder.
     * @param string $expected Expected value.
     */
    public function testShouldReturnSVGPlaceholderWhenWidthHeight($width, $height, $expected)
    {
        Functions\when('absint')->alias(function ($value) {
            return abs(intval($value));
        });

        $this->assertSame(
            $expected,
            $this->image->getPlaceholder($width, $height)
        );
    }

    /**
     * Data provider for testShouldReturnSVGPlaceholderWhenWidthHeight
     *
     * @return array
     */
    public function widthHeightProvider()
    {
        return [
            [
                640,
                480,
                "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 480'%3E%3C/svg%3E",
            ],
            [
                '640',
                '480',
                "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 480'%3E%3C/svg%3E",
            ],
            [
                0,
                0,
                "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E",
            ],
            [
                -1080,
                -640,
                "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1080 640'%3E%3C/svg%3E",
            ],
            [
                'hello',
                'world',
                "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E",
            ],
        ];
    }
}
