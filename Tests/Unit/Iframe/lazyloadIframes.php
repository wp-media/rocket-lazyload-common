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
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( 'wp_parse_args' )->alias( function( $args, $defaults ) {
			if ( is_object( $args ) ) {
				$r = get_object_vars( $args );
			} elseif ( is_array( $args ) ) {
				$r =& $args;
			} else {
				parse_str( $args, $r );
			}

			if ( is_array( $defaults ) ) {
				return array_merge( $defaults, $r );
			}

			return $r;
		} );

		$noiframe = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/noiframe.html' );

		$this->assertSame(
			$noiframe,
			$this->iframe->lazyloadIframes( $noiframe, $noiframe )
		);
	}

	public function testShouldReturnIframeLazyloaded() {
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( 'wp_parse_args' )->alias( function( $args, $defaults ) {
			if ( is_object( $args ) ) {
				$r = get_object_vars( $args );
			} elseif ( is_array( $args ) ) {
				$r =& $args;
			} else {
				parse_str( $args, $r );
			}

			if ( is_array( $defaults ) ) {
				return array_merge( $defaults, $r );
			}

			return $r;
		} );

		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtube.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/iframelazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->iframe->lazyloadIframes( $original, $original )
		);
	}

	public function testShouldReturnYoutubeLazyloaded() {
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'wp_parse_args' )->alias( function( $args, $defaults ) {
			if ( is_object( $args ) ) {
				$r = get_object_vars( $args );
			} elseif ( is_array( $args ) ) {
				$r =& $args;
			} else {
				parse_str( $args, $r );
			}

			if ( is_array( $defaults ) ) {
				return array_merge( $defaults, $r );
			}

			return $r;
		} );
		Functions\when( 'wp_parse_url' )->alias( function( $url, $component ) {
			return parse_url( $url, $component );
		} );

		$args     = [
			'youtube' => true,
		];
		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtube.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/iframe/youtubelazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->iframe->lazyloadIframes( $original, $original, $args )
		);
	}
}
