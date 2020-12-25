<?php

namespace RocketLazyload\Tests\Unit\Image;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;
use RocketLazyload\Image;

/**
 * @covers RocketLazyload\Image::lazyloadBackgroundImages
 *
 * @group Image
 */
class TestLazyloadBackgroundImages extends TestCase {
    private $image;

    public function setUp() {
        parent::setUp();
        Monkey\setUp();
        $this->image = new Image();
    }

    public function tearDown() {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testShouldReturnSameWhenNoBackgroundImage() {
        $noimage = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/noimage.html');

        $this->assertSame(
            $noimage,
            $this->image->lazyloadBackgroundImages($noimage, $noimage)
        );
    }

    public function testShouldReturnBackgroundImagesLazyloaded() {
        Functions\when('esc_attr')->returnArg();

        Functions\expect('esc_url')->andReturnUsing( function ( $url ){
            return str_replace(['"', "'"], ["&quot;", "&#39;"], $url );
        } );

	    Functions\expect('wp_strip_all_tags')->andReturnUsing( function ( $string, $remove_breaks = false ){
		    $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		    $string = strip_tags( $string );

		    if ( $remove_breaks ) {
			    $string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
		    }

		    return trim( $string );
	    } );

        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/bgimages.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Image/bgimageslazyloaded.html');

        $this->assertSame(
            $expected,
            $this->image->lazyloadBackgroundImages($original, $original)
        );
    }
}
