<?php

namespace RocketLazyload\Tests\Unit\Assets;

use RocketLazyload\Assets;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Assets::getNoJSCSS
 */
class Test_GetNoJSCSS extends TestCase {
	private $assets;

	protected function set_up() {
		parent::set_up();
		$this->assets = new Assets();
	}

	public function testShouldReturnCSS() {
		$this->assertSame(
			'<noscript><style id="rocket-lazyload-nojs-css">.rll-youtube-player, [data-lazy-src]{display:none !important;}</style></noscript>',
			$this->assets->getNoJSCSS()
		);
	}
}
