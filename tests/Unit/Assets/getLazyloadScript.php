<?php

namespace RocketLazyload\Tests\Unit\Assets;

use Brain\Monkey\Functions;
use RocketLazyload\Assets;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Assets::getLazyloadScript
 */
class Test_GetLazyloadScript extends TestCase {
	private $assets;

	public function setUp() {
		parent::setUp();
		$this->assets = new Assets();
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testShouldReturnLazyloadScriptWhenScriptDebug() {
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

		define( 'SCRIPT_DEBUG', true );

		$args = [
			'base_url' => 'http://example.org/',
			'version'  => '11.0.2',
		];

		$expected = '<script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.js"></script>';

		$this->assertSame(
			$expected,
			$this->assets->getLazyloadScript( $args )
		);
	}

	public function testShouldReturnMinLazyloadScriptWhenNoScriptDebug() {
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

		$args = [
			'base_url' => 'http://example.org/',
			'version'  => '11.0.2',
		];

		$expected = '<script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.min.js"></script>';

		$this->assertSame(
			$expected,
			$this->assets->getLazyloadScript( $args )
		);
	}

	public function testShouldReturnLazyloadScriptWithPolyfill() {
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

		$args = [
			'base_url' => 'http://example.org/',
			'version'  => '11.0.2',
			'polyfill' => true,
		];

		$expected = '<script crossorigin="anonymous" src="https://polyfill.io/v3/polyfill.min.js?flags=gated&features=default%2CIntersectionObserver%2CIntersectionObserverEntry"></script><script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.min.js"></script>';

		$this->assertSame(
			$expected,
			$this->assets->getLazyloadScript( $args )
		);
	}
}
