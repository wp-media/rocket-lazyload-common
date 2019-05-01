<?php
/**
 * Unit tests for Image::getExcludedAttributes and Image::getExcludedSrc methods
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use PHPUnit\Framework\TestCase;
use RocketLazyload\Image;
use Brain\Monkey;

/**
 * Tests the Image::getExcludedAttributes and Image::getExcludedSrc methods
 */
class TestExcluded extends TestCase
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
     * @covers ::getExcludedAttributes
     * @author Remy Perona
     */
    public function testShouldReturnArrayExcludedAttributes()
    {
        $expected = [
            'data-src=',
            'data-no-lazy=',
            'data-lazy-original=',
            'data-lazy-src=',
            'data-lazysrc=',
            'data-lazyload=',
            'data-bgposition=',
            'data-envira-src=',
            'fullurl=',
            'lazy-slider-img=',
            'data-srcset=',
            'class="ls-l',
            'class="ls-bg',
            'soliloquy-image',
            'loading="auto"',
            'loading="lazy"',
            'loading="eager"',
            'swatch-img',
            'data-height-percentage',

        ];

        $this->assertSame(
            $expected,
            $this->image->getExcludedAttributes()
        );
    }

    /**
     * @covers ::getExcludedSrc
     * @author Remy Perona
     */
    public function testShouldReturnArrayExcludedSrc()
    {
        $expected = [
            '/wpcf7_captcha/',
            'timthumb.php?src',
            'woocommerce/assets/images/placeholder.png',
        ];

        $this->assertSame(
            $expected,
            $this->image->getExcludedSrc()
        );
    }
}
