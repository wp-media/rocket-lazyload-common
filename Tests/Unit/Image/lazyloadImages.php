<?php
declare(strict_types=1);

namespace RocketLazyload\Tests\Unit\Image;

use Brain\Monkey\{Filters, Functions};
use RocketLazyload\Image;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Image::lazyloadImages
 *
 * @uses RocketLazyload\Image::canLazyload
 * @uses RocketLazyload\Image::getExcludedAttributes
 * @uses RocketLazyload\Image::getExcludedSrc
 * @uses RocketLazyload\Image::getPlaceholder
 * @uses RocketLazyload\Image::isExcluded
 * @uses RocketLazyload\Image::noscript
 * @uses RocketLazyload\Image::replaceImage
 *
 * @group Image
 */
class TestLazyloadImages extends TestCase {
	private $image;

	protected function set_up() {
		parent::set_up();

		$this->image = new Image();
	}

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldReturnExpected( $config, $original, $expected ) {
		Functions\when( 'absint' )->alias( function( $value ) {
			return abs( intval( $value ) );
		} );

		Filters\expectApplied( 'rocket_lazyload_noscript' )
			->andReturn( $config['noscript_filter'] );

		$this->assertSame(
			$expected,
			$this->image->lazyloadImages( $original, $original, $config['native'] )
		);
	}
}
