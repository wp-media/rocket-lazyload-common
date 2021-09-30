<?php

namespace Rocketlazyload\Tests\Integration\Image;

use RocketLazyload\Image;
use RocketLazyload\Tests\Integration\TestCase;

/**
 * @covers RocketLazyload\Iframe::lazyloadImages
 * @group  Image
 */
class Test_lazyLoadImages extends TestCase {
	private $image;

	public function setUp() {
		parent::setUp();
		$this->image = new Image();
	}

	public function testShouldReturnSameWhenNoImage() {
		$noimage = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/noimage.html' );

		$this->assertSame(
			$noimage,
			$this->image->lazyloadImages( $noimage, $noimage )
		);
	}

	public function testShouldReturnImagesLazyloadedNoNative() {
		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/images.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/imageslazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadImages( $original, $original, false )
		);
	}

	public function testShouldReturnImagesLazyloadedNative() {
		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/images.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/imageslazyloadednative.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadImages( $original, $original )
		);
	}
}
