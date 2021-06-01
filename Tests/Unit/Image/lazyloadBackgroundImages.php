<?php

namespace RocketLazyload\Tests\Unit\Image;

use Brain\Monkey\Functions;
use RocketLazyload\Image;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Image::lazyloadBackgroundImages
 * @group  Image
 */
class Test_LazyloadBackgroundImages extends TestCase {
	private $image;

	protected function set_up() {
		parent::set_up();
		$this->image = new Image();
	}

	public function testShouldReturnSameWhenNoBackgroundImage() {
		$noimage = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/noimage.html' );

		$this->assertSame(
			$noimage,
			$this->image->lazyloadBackgroundImages( $noimage, $noimage )
		);
	}

	public function testShouldReturnBackgroundImagesLazyloaded() {
		$this->stubEscapeFunctions();

		Functions\when( 'wp_strip_all_tags' )->alias( static function( $string, $remove_breaks = false ) {
			$string = \preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
			$string = \strip_tags( $string );

			if ( $remove_breaks ) {
				$string = \preg_replace( '/[\r\n\t ]+/', ' ', $string );
			}

			return \trim( $string );
		} );

		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/bgimages.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/bgimageslazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $original, $original )
		);
	}
}
