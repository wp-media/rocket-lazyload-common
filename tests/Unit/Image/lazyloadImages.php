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

	public function setUp() {
		parent::setUp();
		$this->image = new Image();
	}

	public function testShouldReturnSameWhenNoImage() {
		$noimage = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/noimage.html' );

		$this->assertSame(
			$noimage,
			$this->image->lazyloadImages( $noimage, $noimage )
		);
	}

	public function testShouldReturnImagesLazyloaded() {
		Functions\when( 'absint' )->alias( function( $value ) {
			return abs( intval( $value ) );
		} );

		$original = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/images.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/imageslazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadImages( $original, $original )
		);
	}
}
