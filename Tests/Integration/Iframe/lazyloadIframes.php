<?php

namespace RocketLazyload\Tests\Integration\Iframe;

use RocketLazyload\Iframe;
use RocketLazyload\RenderToken;
use RocketLazyload\Tests\Integration\TestCase;

/**
 * @covers RocketLazyload\Iframe::lazyloadIframes
 * @group  Iframe
 */
class Test_LazyloadIframes extends TestCase {
	private $iframe;

	public function set_up() {
		parent::set_up();
		$this->iframe = new Iframe();

		// Deterministic token so the youtubelazyloaded.html fixture can assert
		// on an exact value; shared with the unit test's own fixture copy.
		( new RenderToken() )->set( '0123456789abcdef' );
	}

	public function tear_down() {
		( new RenderToken() )->reset();
		parent::tear_down();
	}

	public function testShouldReturnSameWhenNoIframe() {
		$noiframe = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/noiframe.html' );

		$this->assertSame(
			$noiframe,
			$this->iframe->lazyloadIframes( $noiframe, $noiframe )
		);
	}

	public function testShouldReturnIframeLazyloaded() {
		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtube.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/iframelazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->iframe->lazyloadIframes( $original, $original )
		);
	}

	public function testShouldReturnYoutubeLazyloaded() {
		$args     = [ 'youtube' => true ];
		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtube.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtubelazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->iframe->lazyloadIframes( $original, $original, $args )
		);
	}
}
