<?php

namespace RocketLazyload\Tests\Unit\Iframe;

use Brain\Monkey\Functions;
use RocketLazyload\Iframe;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Iframe::lazyloadIframes
 * @group  Iframe
 */
class Test_LazyloadIframe extends TestCase {
	private $iframe;

	public function setUp() {
		parent::setUp();
		$this->iframe = new Iframe();
	}

	public function testShouldReturnSameWhenNoIframe() {
		$defaults = [
            'youtube' => false,
		];

		Functions\when( 'esc_url' )->returnArg();
		Functions\when( 'wp_parse_args' )->justReturn( $defaults );

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

		Functions\when( 'esc_url' )->returnArg();
		Functions\when( 'wp_parse_args' )->justReturn( $defaults );

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

		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'wp_parse_args' )->justReturn( $args );
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
