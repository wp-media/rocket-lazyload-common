<?php
/**
 * Unit tests for RocketLazyload\Image::lazyloadResponsiveAttributes method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Image;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Image;

/**
 * Tests for the RocketLazyload\Image::lazyloadResponsiveAttributes method
 *
 * @covers RocketLazyload\Image::lazyloadResponsiveAttributes
 * @group Image
 */
class TestLazyloadResponsiveAttributes extends TestCase
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
     * Test should lazyload responsive attributes when they exists in the image
     */
    public function testShouldLazyloadResponsiveAttributesWhenPresent()
    {
        $image    = '<img width="300" height="580" src="http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1.jpg" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="Image à la une verticale" srcset="http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1.jpg 300w, http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1-155x300.jpg 155w, http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1-10x20.jpg 10w" sizes="(max-width: 300px) 100vw, 300px" />';
        $expected = '<img width="300" height="580" src="http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1.jpg" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="Image à la une verticale" data-lazy-srcset="http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1.jpg 300w, http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1-155x300.jpg 155w, http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1-10x20.jpg 10w" data-lazy-sizes="(max-width: 300px) 100vw, 300px" />';

        $this->assertSame(
            $expected,
            $this->image->lazyloadResponsiveAttributes($image)
        );
    }

    /**
     * Test should do nothing when there is no responsive attributes
     */
    public function testShouldDoNothingWhenNoResponsiveAttributes()
    {
        $image    = '<img width="300" height="580" src="http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1.jpg" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="Image à la une verticale" />';
        $expected = '<img width="300" height="580" src="http://wordpress.test/wp-content/uploads/2013/03/image-a-la-une-verticale-1.jpg" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="Image à la une verticale" />';

        $this->assertSame(
            $expected,
            $this->image->lazyloadResponsiveAttributes($image)
        );
    }
}
