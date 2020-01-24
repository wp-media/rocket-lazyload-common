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
		$args = [
			'base_url' => 'http://example.org/',
			'version'  => '11.0.2',
		];

		$parsed_args = [
            'base_url' => 'http://example.org/',
			'version'  => '11.0.2',
            'polyfill' => false,
		];

		Functions\when( 'wp_parse_args' )->justReturn( $parsed_args );

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

		$parsed_args = [
            'base_url' => 'http://example.org/',
			'version'  => '11.0.2',
            'polyfill' => false,
		];

		Functions\when( 'wp_parse_args' )->justReturn( $parsed_args );

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

		$parsed_args = [
            'base_url' => 'http://example.org/',
			'version'  => '11.0.2',
            'polyfill' => true,
		];

		Functions\when( 'wp_parse_args' )->justReturn( $parsed_args );

		$expected = '<script crossorigin="anonymous" src="https://polyfill.io/v3/polyfill.min.js?flags=gated&features=default%2CIntersectionObserver%2CIntersectionObserverEntry"></script><script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.min.js"></script>';

		$this->assertSame(
			$expected,
			$this->assets->getLazyloadScript( $args )
		);
	}
}
