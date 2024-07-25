<?php
declare(strict_types=1);

namespace Rocketlazyload\Tests\Integration\Image;

use RocketLazyload\Image;
use RocketLazyload\Tests\Integration\TestCase;

/**
 * @covers RocketLazyload\Iframe::lazyloadImages
 *
 * @group Image
 */
class Test_lazyLoadImages extends TestCase {
	private $image;
	private $noscript;

	public function set_up() {
		parent::set_up();
		$this->image = new Image();
	}

	public function tear_down() {
		remove_filter( 'rocket_lazyload_noscript', [ $this, 'noscript' ] );

		parent::tear_down();
	}

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldReturnExpected( $config, $original, $expected ) {
		$this->noscript = $config['noscript_filter'];

		add_filter( 'rocket_lazyload_noscript', [ $this, 'noscript' ] );

		$this->assertSame(
			$expected,
			$this->image->lazyloadImages( $original, $original, $config['native'] )
		);
	}

	public function noscript() {
		return $this->noscript;
	}
}
