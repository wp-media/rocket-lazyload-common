<?php

namespace RocketLazyload\Tests\Unit\Iframe;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use RocketLazyload\Iframe;
use RocketLazyload\RenderToken;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Iframe::lazyloadIframes
 * @uses RocketLazyload\Iframe::addRenderToken
 * @uses RocketLazyload\Iframe::getExcludedPatterns
 * @uses RocketLazyload\Iframe::isIframeExcluded
 * @uses RocketLazyload\Iframe::replaceIframe
 * @uses RocketLazyload\Iframe::changeYoutubeUrlForYoutuDotBe
 * @uses RocketLazyload\Iframe::cleanYoutubeUrl
 * @uses RocketLazyload\Iframe::getYoutubeIDFromURL
 * @uses RocketLazyload\Iframe::replaceYoutubeThumbnail
 * @uses RocketLazyload\RenderToken::get
 * @group  Iframe
 */
class Test_LazyloadIframe extends TestCase {
	private $iframe;

	protected function set_up() {
		parent::set_up();
		$this->iframe = new Iframe();

		// Deterministic token so fixtures can assert on an exact value.
		RenderToken::reset( '0123456789abcdef' );

		$this->stubEscapeFunctions();

		Functions\when( 'wp_parse_args' )->alias( static function ( $parsed_args, $defaults ) {
			return \array_merge( $defaults, $parsed_args );
		} );
	}

	protected function tear_down() {
		RenderToken::reset();
		parent::tear_down();
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

	/**
	 * A site filter that customises the placeholder's other attributes/classes,
	 * but keeps the `rll-youtube-player` class, must still end up with the
	 * render token: the token is added after the filter runs, targeting
	 * whichever element still carries that class, so customised filters keep
	 * their thumbnails working.
	 */
	public function testShouldAddTokenAfterFilterWhenClassIsKept() {
		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		Filters\expectApplied( 'rocket_lazyload_youtube_html' )
			->andReturn( '<div class="rll-youtube-player custom-theme" data-extra="1"></div>' );

		$html   = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
		$output = $this->iframe->lazyloadIframes( $html, $html, [ 'youtube' => true ] );

		$this->assertStringContainsString(
			'<div data-rll-token="0123456789abcdef" class="rll-youtube-player custom-theme" data-extra="1"></div>',
			$output
		);
	}

	/**
	 * A site filter that removes the `rll-youtube-player` class entirely
	 * already opts that element out of the whole feature (the client-side
	 * script only ever looks for that class); no token is added, and nothing
	 * breaks.
	 */
	public function testShouldNotAddTokenWhenFilterRemovesTheClass() {
		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		Filters\expectApplied( 'rocket_lazyload_youtube_html' )
			->andReturn( '<div class="totally-different"></div>' );

		$html   = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
		$output = $this->iframe->lazyloadIframes( $html, $html, [ 'youtube' => true ] );

		$this->assertStringContainsString( '<div class="totally-different"></div>', $output );
		$this->assertStringNotContainsString( 'data-rll-token', $output );
	}
}
