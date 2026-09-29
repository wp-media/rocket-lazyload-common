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
 * @uses RocketLazyload\Iframe::addRenderTokenToDivTag
 * @uses RocketLazyload\Iframe::findAttribute
 * @uses RocketLazyload\Iframe::hasClassToken
 * @uses RocketLazyload\Iframe::stripOuterQuoteChars
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
		( new RenderToken() )->set( '0123456789abcdef' );

		$this->stubEscapeFunctions();

		Functions\when( 'wp_parse_args' )->alias( static function ( $parsed_args, $defaults ) {
			return \array_merge( $defaults, $parsed_args );
		} );
	}

	protected function tear_down() {
		( new RenderToken() )->reset();
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

	/**
	 * @dataProvider tokenPlacementProvider
	 */
	public function testShouldAddTokenAcrossAttributeOrderAndQuoteStyles( $filtered_html, $expected_needle ) {
		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		Filters\expectApplied( 'rocket_lazyload_youtube_html' )
			->andReturn( $filtered_html );

		$html   = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
		$output = $this->iframe->lazyloadIframes( $html, $html, [ 'youtube' => true ] );

		$this->assertStringContainsString( $expected_needle, $output );
	}

	public function tokenPlacementProvider() {
		return [
			'class not first'          => [
				'<div id="x" class="rll-youtube-player"></div>',
				'<div data-rll-token="0123456789abcdef" id="x" class="rll-youtube-player"></div>',
			],
			'single-quoted class'      => [
				"<div class='rll-youtube-player'></div>",
				"<div data-rll-token=\"0123456789abcdef\" class='rll-youtube-player'></div>",
			],
			'unquoted class'           => [
				'<div class=rll-youtube-player></div>',
				'<div data-rll-token="0123456789abcdef" class=rll-youtube-player></div>',
			],
		];
	}

	/**
	 * Every genuine `.rll-youtube-player` placeholder in the filtered output
	 * gets its own token, not just the first one.
	 */
	public function testShouldAddTokenToEveryPlaceholderInFilteredOutput() {
		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		Filters\expectApplied( 'rocket_lazyload_youtube_html' )
			->andReturn( '<div class="rll-youtube-player"></div><div class="rll-youtube-player"></div>' );

		$html   = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
		$output = $this->iframe->lazyloadIframes( $html, $html, [ 'youtube' => true ] );

		$this->assertSame( 2, substr_count( $output, 'data-rll-token="0123456789abcdef"' ) );
	}

	/**
	 * A class token that merely starts with `rll-youtube-player` (a different,
	 * longer class name) is not a match: class tokens are compared whole,
	 * whitespace-separated, the same way a browser matches CSS classes.
	 */
	public function testShouldNotMatchClassNameThatOnlyStartsWithTheToken() {
		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		Filters\expectApplied( 'rocket_lazyload_youtube_html' )
			->andReturn( '<div class="rll-youtube-player-extra"></div>' );

		$html   = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
		$output = $this->iframe->lazyloadIframes( $html, $html, [ 'youtube' => true ] );

		$this->assertStringContainsString( '<div class="rll-youtube-player-extra"></div>', $output );
		$this->assertStringNotContainsString( 'data-rll-token', $output );
	}

	/**
	 * A `class=`-shaped substring nested inside another attribute's value (e.g.
	 * title) is never mistaken for a real class attribute.
	 */
	public function testShouldNotMatchClassTextNestedInsideAnotherAttributeValue() {
		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		Filters\expectApplied( 'rocket_lazyload_youtube_html' )
			->andReturn( '<div title="class=rll-youtube-player"></div>' );

		$html   = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
		$output = $this->iframe->lazyloadIframes( $html, $html, [ 'youtube' => true ] );

		$this->assertStringContainsString( '<div title="class=rll-youtube-player"></div>', $output );
		$this->assertStringNotContainsString( 'data-rll-token', $output );
	}

	/**
	 * A placeholder that already carries a data-rll-token (e.g. hand-authored
	 * directly in content) is left exactly as-is, not overwritten or
	 * duplicated: the script compares it against the current token, so a
	 * stale/foreign value simply never matches.
	 */
	public function testShouldNotDuplicateAnExistingDataRllToken() {
		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		Filters\expectApplied( 'rocket_lazyload_youtube_html' )
			->andReturn( '<div data-rll-token="not-the-real-one" class="rll-youtube-player"></div>' );

		$html   = '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
		$output = $this->iframe->lazyloadIframes( $html, $html, [ 'youtube' => true ] );

		$this->assertStringContainsString( '<div data-rll-token="not-the-real-one" class="rll-youtube-player"></div>', $output );
		$this->assertSame( 1, substr_count( $output, 'data-rll-token' ) );
	}
}
