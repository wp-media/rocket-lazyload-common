<?php

namespace RocketLazyload\Tests\Unit\RenderToken;

use Brain\Monkey\Functions;
use RocketLazyload\Assets;
use RocketLazyload\Iframe;
use RocketLazyload\RenderToken;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * Confirms Iframe and Assets read the same per-request token from RenderToken,
 * without either of them needing to know about the other.
 *
 * @covers RocketLazyload\Iframe::replaceYoutubeThumbnail
 * @covers RocketLazyload\Assets::getYoutubeThumbnailScript
 * @uses RocketLazyload\Iframe::lazyloadIframes
 * @uses RocketLazyload\Iframe::isIframeExcluded
 * @uses RocketLazyload\Iframe::getExcludedPatterns
 * @uses RocketLazyload\Iframe::addRenderToken
 * @uses RocketLazyload\Iframe::getYoutubeIDFromURL
 * @uses RocketLazyload\Iframe::changeYoutubeUrlForYoutuDotBe
 * @uses RocketLazyload\Iframe::cleanYoutubeUrl
 * @uses RocketLazyload\RenderToken::get
 * @group  RenderToken
 */
class Test_Pairing extends TestCase {
	protected function tear_down() {
		RenderToken::reset();
		parent::tear_down();
	}

	public function testIframeAndAssetsShouldEmbedTheSameTokenInOneRequest() {
		RenderToken::reset();

		$this->stubEscapeFunctions();

		Functions\when( 'wp_parse_args' )->alias( static function ( $parsed_args, $defaults ) {
			return \array_merge( $defaults, $parsed_args );
		} );
		Functions\when( 'wp_parse_url' )->alias( static function ( $url, $component ) {
			return \parse_url( $url, $component );
		} );

		$iframe_html = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
		$placeholder = ( new Iframe() )->lazyloadIframes( $iframe_html, $iframe_html, [ 'youtube' => true ] );

		$this->assertMatchesRegularExpression( '/data-rll-token="(?<token>[0-9a-f]+)"/', $placeholder );
		preg_match( '/data-rll-token="(?<token>[0-9a-f]+)"/', $placeholder, $matches );
		$iframe_token = $matches['token'];

		$script = ( new Assets() )->getYoutubeThumbnailScript();

		$this->assertMatchesRegularExpression( '/var token="(?<token>[0-9a-f]+)";/', $script );
		preg_match( '/var token="(?<token>[0-9a-f]+)";/', $script, $matches );
		$assets_token = $matches['token'];

		$this->assertSame( $iframe_token, $assets_token );
	}
}
