<?php

namespace RocketLazyload\Tests\Integration\Assets;

use RocketLazyload\Assets;
use RocketLazyload\Tests\Integration\TestCase;

/**
 * @covers RocketLazyload\Assets::insertYoutubeThumbnailCSS
 * @group  Image
 */
class Test_InsertYoutubeThumbnailCSS extends TestCase {
	private $assets;

	protected function set_up() {
		parent::set_up();
		$this->assets = new Assets();
	}

	public function testShouldAddYoutubeThumbnailCSSWithResponsive() {
		$this->assets->insertYoutubeThumbnailCSS(
			[
				'base_url' => plugins_url() . '/assets/',
			]
		);

		$this->assertTrue( wp_style_is( 'rocket-lazyload' ) );

		$this->assertContains(
			'.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player:focus-within{outline: 2px solid currentColor;outline-offset: 5px;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:100%;width:100%;left:0;top:0;position:absolute;background:url(http://example.org/wp-content/plugins/assets/img/youtube.png) no-repeat center;cursor:pointer;border:none;}.wp-embed-responsive .wp-has-aspect-ratio .rll-youtube-player{position:absolute;padding-bottom:0;width:100%;height:100%;top:0;bottom:0;left:0;right:0}',
			wp_styles()->print_inline_style( 'rocket-lazyload', false )
		);
	}

	public function testShouldAddYoutubeThumbnailCSSWithoutResponsive() {
		$this->assets->insertYoutubeThumbnailCSS(
			[
				'base_url'          => plugins_url() . '/assets/',
				'responsive_embeds' => false,
			]
		);

		$this->assertTrue( wp_style_is( 'rocket-lazyload' ) );

		$this->assertContains(
			'.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player:focus-within{outline: 2px solid currentColor;outline-offset: 5px;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:100%;width:100%;left:0;top:0;position:absolute;background:url(http://example.org/wp-content/plugins/assets/img/youtube.png) no-repeat center;cursor:pointer;border:none;}',
			wp_styles()->print_inline_style( 'rocket-lazyload', false )
		);
	}
}
