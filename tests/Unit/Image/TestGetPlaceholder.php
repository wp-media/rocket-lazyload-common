<?php
/**
 * Unit tests for Image::getPlaceholder method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use PHPUnit\Framework\TestCase;
use RocketLazyload\Image;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Tests for the mage::getPlaceholder method
 *
 * @coversDefaultClass RocketLazyload\Image
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
        $this->image = new Image();
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * Do this after each test
     *
     * @return void
     */
    public function tearDown()
    {
        Monkey\TearDown();
        parent::tearDown();
    }

    /**
     * @covers ::getPlaceholder
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
     * @covers ::getPlaceholder
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
