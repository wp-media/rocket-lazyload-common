<?php
/**
 * Unit tests for RocketLazyload\Image::getPlaceholder method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Image;
use Brain\Monkey\Functions;

/**
 * Tests for the RocketLazyload\Image::getPlaceholder method
 *
 * @covers \RocketLazyload\Image::getPlaceholder
 * @group Image
 */
class TestGetPlaceholder extends TestCase
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
     * Test should return default SVG placeholder when there is no arguments
     */
    public function testShouldReturnSVGPlaceholderWhenNoArguments()
    {
        Functions\when('absint')->alias(function ($value) {
            return abs(intval($value));
        });

        $placeholder = "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E";

        $this->assertSame(
            $placeholder,
            $this->image->getPlaceholder()
        );
    }

    /**
     * Test should return SVG placeholder with custom width & height
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
                "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20640%20480'%3E%3C/svg%3E",
            ],
            [
                '640',
                '480',
                "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20640%20480'%3E%3C/svg%3E",
            ],
            [
                0,
                0,
                "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E",
            ],
            [
                -1080,
                -640,
                "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201080%20640'%3E%3C/svg%3E",
            ],
            [
                'hello',
                'world',
                "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%200%200'%3E%3C/svg%3E",
            ],
        ];
    }
}
