<?php
namespace Rocketlazyload\Tests\Integration;

use WP_UnitTestCase;
use RocketLazyload\Assets;

class TestAssets extends WP_UnitTestCase
{
    private $assets;

    public function setUp()
    {
        $this->assets = new Assets();
    }

    public function testAddYoutubeThumbnailCSS()
    {
        global $wp_styles;

        $this->assets->insertYoutubeThumbnailCSS(
            [
                'base_url' => plugins_url() . '/assets/',
            ]
        );

        $this->assertArrayHasKey(
            'rocket-lazyload',
            $wp_styles->registered
        );

        $this->assertContains(
            'rocket-lazyload',
            $wp_styles->queue
        );

        $this->assertContains(
            '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(http://example.org/wp-content/plugins/assets/img/youtube.png) no-repeat;cursor:pointer}.wp-has-aspect-ratio .rll-youtube-player{position:absolute;padding-bottom:0;width:100%;height:100%;top:0;bottom:0;left:0;right:0;',
            $wp_styles->registered['rocket-lazyload']->extra['after']
        );
    }
}
