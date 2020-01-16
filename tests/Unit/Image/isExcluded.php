<?php
/**
 * Unit tests for RocketLazyload\Image::isExcluded method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Image;

/**
 * Tests for the RocketLazyload\Image::isExcluded method
 *
 * @covers \RocketLazyload\Image::isExcluded
 * @group Image
 */
class TestIsExcluded extends TestCase
{
    /**
     * Instance of Image
     *
     * @var Image
     */
    private $image;

    /**
     * Excluded source patterns
     *
     * @var array
     */
    private $excluded_src = [
        '/wpcf7_captcha/',
        'timthumb.php?src',
        'woocommerce/assets/images/placeholder.png',
    ];

    /**
     * Excluded attributes patterns
     *
     * @var array
     */
    private $excluded_attributes = [
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
        'loading="eager"',
        'swatch-img',
        'data-height-percentage',
        'data-large_image',
        'avia-bg-style-fixed',
        'data-skip-lazy',
        'skip-lazy',
    ];

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
     * Test should return true when an excluded pattern is found
     *
     * @dataProvider imageWithExcludedPatternProvider
     *
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
        return [
            [
                'http://example.org/wpcf7_captcha/captcha.gif',
                $this->excluded_src,
            ],
            [
                'http://example.org/timthumb.php?src=image.jpg',
                $this->excluded_src,
            ],
            [
                'http://example.org/wp-content/plugins/woocommerce/assets/images/placeholder.png',
                $this->excluded_src,
            ],
            [
                'data-src="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-no-lazy="1" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-lazy-original="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-lazy-src="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-lazysrc="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-lazyload="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-bgposition="top right" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-envira-src="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'fullurl="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'lazy-slider-img="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-srcset="http://example.org/image.jpg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'class="ls-l fullwidth" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'class="ls-bg" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'class="swatch-img" width="200" height="200" alt=""',
                $this->excluded_attributes,
            ],
            [
                'data-height-percentage="54"',
                $this->excluded_attributes,
            ],
            [
                'data-caption="" data-large_image="https://tst.japanese-knives.co.il/wp-content/uploads/Tai-400-1200-_DSC4332.jpg" data-large_image_width="1200" data-large_image_height="800" srcset="https://tst.japanese-knives.co.il/wp-content/uploads/Tai-400-1200-_DSC4332-247x165.jpg 247w, https://tst.japanese-knives.co.il/wp-content/uploads/Tai-400-1200-_DSC4332-510x340.jpg 510w, https://tst.japanese-knives.co.il/wp-content/uploads/Tai-400-1200-_DSC4332-350x233.jpg 350w, https://tst.japanese-knives.co.il/wp-content/uploads/Tai-400-1200-_DSC4332-768x512.jpg 768w, https://tst.japanese-knives.co.il/wp-content/uploads/Tai-400-1200-_DSC4332-1024x683.jpg 1024w, https://tst.japanese-knives.co.il/wp-content/uploads/Tai-400-1200-_DSC4332.jpg 1200w" sizes="(max-width: 247px) 100vw, 247px"',
                $this->excluded_attributes,
            ],
            [
                'class="avia-bg-style-fixed" style="background-image:url(example.jpg)" data-section-bg="repeat"',
                $this->excluded_attributes,
            ],
            [
                'class="skip-lazy" style="background-image:url(example.jpg)" data-section-bg="repeat"',
                $this->excluded_attributes,
            ],
            [
                'data-skip-lazy="1" style="background-image:url(example.jpg)" data-section-bg="repeat"',
                $this->excluded_attributes,
            ],
        ];
    }

    /**
     * Test should return false when the excluded values array is empty
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
     * Test should return false when no excluded patterns are found
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
        return [
            [
                'http://example.com/image.jpg',
                $this->excluded_src,
            ],
            [
                'class="wp-image wp-image-size-medium" width="640" height="480" alt=""',
                $this->excluded_attributes,
            ],
        ];
    }
}
