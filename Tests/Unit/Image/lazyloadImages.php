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
		$this->stubEscapeFunctions();

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

	/**
	 * esc_url() is deliberately not stubbed: in WordPress it strips the data: protocol,
	 * so reaching it for a data: image would empty data-lazy-src.
	 *
	 * @dataProvider dataUriImageProvider
	 */
	public function testShouldNotLazyloadDataUriImages( $html, $native ) {
		$this->assertSame(
			$html,
			$this->image->lazyloadImages( $html, $html, $native )
		);
	}

	public function dataUriImageProvider() {
		$base64 = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" alt="pixel" width="1" height="1">';
		$svg    = "<img src='data:image/svg+xml,%3Csvg%20xmlns=%22http://www.w3.org/2000/svg%22%3E%3C/svg%3E' alt='svg'>";
		$upper  = '<img src=" DATA:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="upper">';

		return [
			'base64 png, JS lazyload'   => [ $base64, false ],
			'base64 png, native'        => [ $base64, true ],
			'svg, single quotes, JS'    => [ $svg, false ],
			'uppercase, padded src, JS' => [ $upper, false ],
		];
	}
}
