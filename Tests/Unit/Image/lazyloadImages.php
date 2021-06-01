<?php

namespace RocketLazyload\Tests\Unit\Image;

use Brain\Monkey\Functions;
use RocketLazyload\Image;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Image::lazyloadImages
 * @group  Image
 */
class Test_LazyloadImages extends TestCase {
	private $image;

	protected function set_up() {
		parent::set_up();
		$this->image = new Image();
	}

	public function testShouldReturnSameWhenNoImage() {
		$noimage = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/noimage.html' );

		$this->assertSame(
			$noimage,
			$this->image->lazyloadImages( $noimage, $noimage )
		);
	}

	public function testShouldReturnImagesLazyloaded() {
		Functions\when( 'absint' )->alias( function( $value ) {
			return abs( intval( $value ) );
		} );

		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/images.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/imageslazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadImages( $original, $original )
		);
	}
}
