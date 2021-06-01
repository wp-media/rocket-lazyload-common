<?php

namespace RocketLazyload\Tests\Unit\Iframe;

use Brain\Monkey\Functions;
use RocketLazyload\Iframe;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Iframe::lazyloadIframes
 * @uses RocketLazyload\Iframe::getExcludedPatterns
 * @uses RocketLazyload\Iframe::isIframeExcluded
 * @uses RocketLazyload\Iframe::replaceIframe
 * @uses RocketLazyload\Iframe::changeYoutubeUrlForYoutuDotBe
 * @uses RocketLazyload\Iframe::cleanYoutubeUrl
 * @uses RocketLazyload\Iframe::getYoutubeIDFromURL
 * @uses RocketLazyload\Iframe::replaceYoutubeThumbnail
 * @group  Iframe
 */
class Test_LazyloadIframe extends TestCase {
	private $iframe;

	protected function set_up() {
		parent::set_up();
		$this->iframe = new Iframe();

		$this->stubEscapeFunctions();

		Functions\when( 'wp_parse_args' )->alias( static function ( $parsed_args, $defaults ) {
			return \array_merge( $defaults, $parsed_args );
		} );
	}

	public function testShouldReturnSameWhenNoIframe() {
		$defaults = [
            'youtube' => false,
		];

		$noiframe = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/noiframe.html' );

		$this->assertSame(
			$noiframe,
			$this->iframe->lazyloadIframes( $noiframe, $noiframe )
		);
	}

	public function testShouldReturnIframeLazyloaded() {
		$defaults = [
            'youtube' => false,
		];

		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtube.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/iframelazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->iframe->lazyloadIframes( $original, $original )
		);
	}

	public function testShouldReturnYoutubeLazyloaded() {
		$args     = [
			'youtube' => true,
		];

		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtube.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtubelazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->iframe->lazyloadIframes( $original, $original, $args )
		);
	}
}
