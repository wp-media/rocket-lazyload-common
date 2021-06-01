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

	protected function set_up() {
		parent::set_up();
		$this->assets = new Assets();

		Functions\when( 'wp_parse_args' )->alias( static function ( $parsed_args, $defaults ) {
			return \array_merge( $defaults, $parsed_args );
		} );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testShouldReturnLazyloadScriptWhenScriptDebug() {
		$args = [
			'base_url' => 'http://example.org/',
			'version'  => '11.0.2',
		];

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
