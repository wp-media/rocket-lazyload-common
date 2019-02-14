<?php
/**
 * Unit tests for RocketLazyload\Image::isExcluded method
 *
 * @package RocketLazyload\Tests\Unit\Image
 */

namespace RocketLazyload\Tests\Unit\Image;

use PHPUnit\Framework\TestCase;
use RocketLazyload\Image;

/**
 * Tests for the RocketLazyload\Image::isExcluded method
 */
class TestIsExcluded extends TestCase
{
    /**
     * Instance of RocketLazyload\Image
     *
     * @var RocketLazyload\Image
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
     * Tests the method returns true when an excluded pattern is contained in the provided string
     *
     * @dataProvider imageWithExcludedPatternProvider
     * @param string $string          String to test.
     * @param string $excluded_values Values to test the pattern against.
     */
    public function testShouldReturnTrueWhenPatternIsExcluded($string, $excluded_values)
    {
        $this->assertTrue(
            $this->image->isExcluded($string, $excluded_values)
        );
    }

    /**
     * Data Provider for testShouldReturnTrueWhenPatternIsExcluded
     *
     * @return array
     */
    public function imageWithExcludedPatternProvider()
    {
        $excluded_src = [
            '/wpcf7_captcha/',
            'timthumb.php?src',
        ];

        $excluded_attributes = [
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
        ];

        return [
            [
                'http://example.org/wpcf7_captcha/captcha.gif',
                $excluded_src,
            ],
            [
                'http://example.org/timthumb.php?src=image.jpg',
                $excluded_src,
            ],
            [
                'data-src="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'data-no-lazy="1" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'data-lazy-original="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'data-lazy-src="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'data-lazysrc="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'data-lazyload="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'data-bgposition="top right" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'data-envira-src="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'fullurl="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'lazy-slider-img="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'data-srcset="http://example.org/image.jpg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'class="ls-l fullwidth" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
            [
                'class="ls-bg" width="200" height="200" alt=""',
                $excluded_attributes,
            ],
        ];
    }

    /**
     * Test the method returns false when the excluded values parameters is an empty array
     */
    public function testShouldReturnFalseWhenEmptyExcludedValues()
    {
        $this->assertFalse(
            $this->image->isExcluded(
                'data-srcset="http://example.org/image.jpg" width="200" height="200" alt=""',
                []
            )
        );
    }

    /**
     * Tests the method returns false when no excluded pattern is contained in the provided string
     *
     * @dataProvider imageWithoutExcludedPatternProvider
     * @param string $string          String to test.
     * @param string $excluded_values Values to test the pattern against.
     */
    public function testShouldReturnFalseWhenNoPatternExcluded($string, $excluded_values)
    {
        $this->assertFalse(
            $this->image->isExcluded($string, $excluded_values)
        );
    }

    /**
     * Data provider for testShouldReturnFalseWhenNoPatternExcluded
     *
     * @return array
     */
    public function imageWithoutExcludedPatternProvider()
    {
        $excluded_src = [
            '/wpcf7_captcha/',
            'timthumb.php?src',
        ];

        $excluded_attributes = [
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
        ];

        return [
            [
                'http://example.com/image.jpg',
                $excluded_src,
            ],
            [
                'class="wp-image wp-image-size-medium" width="640" height="480" alt=""',
                $excluded_attributes,
            ],
        ];
    }
}
