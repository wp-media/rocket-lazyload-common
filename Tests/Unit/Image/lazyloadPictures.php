<?php

namespace RocketLazyload\Tests\Unit\Image;

use Brain\Monkey\Functions;
use RocketLazyload\Image;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Image::lazyloadPictures
 * @group  Image
 */
class TestLazyloadPictures extends TestCase {
	private $image;

	public function set_up() {
		parent::set_up();
		$this->image = new Image();
	}

	public function testShouldReturnSameWhenNoPicture() {
		$noimage = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/noimage.html' );

		$this->assertSame(
			$noimage,
			$this->image->lazyloadPictures( $noimage, $noimage )
		);
	}

	public function testShouldReturnPicturesLazyloaded() {
		Functions\when( 'absint' )->alias( function( $value ) {
			return abs( intval( $value ) );
		} );

		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/pictures.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/pictureslazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadPictures( $original, $original )
		);
	}
}
