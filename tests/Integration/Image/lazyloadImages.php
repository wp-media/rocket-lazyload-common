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
		$noimage = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/noimage.html' );

		$this->assertSame(
			$noimage,
			$this->image->lazyloadImages( $noimage, $noimage )
		);
	}

	/**
	 * Test should return HTML with images lazyloaded
	 */
	public function testShouldReturnImagesLazyloaded() {
		$original = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/images.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/imageslazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadImages( $original, $original )
		);
	}
}
