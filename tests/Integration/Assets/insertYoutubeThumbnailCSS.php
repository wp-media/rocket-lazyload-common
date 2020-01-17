<?php
/**
 * Integration tests for the RocketLazyload\Assets::insertYoutubeThumbnailCSS method
 *
 * @package RocketLazyload\Tests\Integration
 */

namespace RocketLazyload\Tests\Integration;

use RocketLazyload\Tests\Integration\TestCase;
use RocketLazyload\Assets;

/**
 * Integration tests for the RocketLazyload\Assets::insertYoutubeThumbnailCSS method
 *
 * @covers \RocketLazyload\Assets::insertYoutubeThumbnailCSS
 * @group Image
 */
class TestInsertYoutubeThumbnailCSS extends TestCase
{
    /**
     * Assets instance
     *
     * @var Assets
     */
    private $assets;

    /**
     * Do this before each test
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->assets = new Assets();
    }

    /**
     * Test should add youtube thumbnail CSS with responsive styles
     */
    public function testShouldAddYoutubeThumbnailCSSWithResponsive()
    {
        $this->assets->insertYoutubeThumbnailCSS(
            [
                'base_url' => plugins_url() . '/assets/',
            ]
        );

        $this->assertTrue(wp_style_is('rocket-lazyload'));

        $this->assertContains(
            '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(http://example.org/wp-content/plugins/assets/img/youtube.png) no-repeat;cursor:pointer}.wp-has-aspect-ratio .rll-youtube-player{position:absolute;padding-bottom:0;width:100%;height:100%;top:0;bottom:0;left:0;right:0}',
            wp_styles()->print_inline_style('rocket-lazyload', false)
        );
    }

    /**
     * Test should add youtube thumbnail CSS CSS without responsive styles
     */
    public function testShouldAddYoutubeThumbnailCSSWithoutResponsive()
    {
        $this->assets->insertYoutubeThumbnailCSS(
            [
                'base_url'          => plugins_url() . '/assets/',
                'responsive_embeds' => false,
            ]
        );

        $this->assertTrue(wp_style_is('rocket-lazyload'));

        $this->assertContains(
            '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(http://example.org/wp-content/plugins/assets/img/youtube.png) no-repeat;cursor:pointer}',
            wp_styles()->print_inline_style('rocket-lazyload', false)
        );
    }
}
