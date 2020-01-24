<?php

namespace RocketLazyload\Tests\Unit\Assets;

use Brain\Monkey\Functions;
use RocketLazyload\Assets;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Assets::getYoutubeThumbnailCSS
 */
class Test_GetYoutubeThumbnailCSS extends TestCase {
	private $assets;

	public function setUp() {
		parent::setUp();
		$this->assets = new Assets();
	}

	public function testShouldReturnYoutubeThumbnailCSSWithResponsive() {
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
		];

		$expected = '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(http://example.org/img/youtube.png) no-repeat;cursor:pointer}.wp-has-aspect-ratio .rll-youtube-player{position:absolute;padding-bottom:0;width:100%;height:100%;top:0;bottom:0;left:0;right:0}';

		$this->assertSame(
			$expected,
			$this->assets->getYoutubeThumbnailCSS( $args )
		);
	}

	public function testShouldReturnYoutubeThumbnailCSSWithoutResponsive() {
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
			'base_url'          => 'http://example.org/',
			'responsive_embeds' => false,
		];

		$expected = '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(http://example.org/img/youtube.png) no-repeat;cursor:pointer}';

		$this->assertSame(
			$expected,
			$this->assets->getYoutubeThumbnailCSS( $args )
		);
	}
}
