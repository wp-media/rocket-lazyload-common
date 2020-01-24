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

	public function setUp() {
		parent::setUp();
		$this->image = new Image();
	}

	public function testShouldReturnSameWhenNoBackgroundImage() {
		$noimage = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/noimage.html' );

		$this->assertSame(
			$noimage,
			$this->image->lazyloadBackgroundImages( $noimage, $noimage )
		);
	}

	public function testShouldReturnBackgroundImagesLazyloaded() {
		Functions\when( 'esc_attr' )->returnArg();

		$original = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/bgimages.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'tests/Fixtures/image/bgimageslazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $original, $original )
		);
	}
}
