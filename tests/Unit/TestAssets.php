<?php
/**
 * Unit tests for the Assets methods
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use RocketLazyload\Assets;

/**
 * Unit test for the Assets methods
 *
 * @coversDefaultClass RocketLazyload\Assets
 */
class TestAssets extends TestCase
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
        $this->assets = new Assets();
    }

    /**
     * Do this after each test
     *
     * @return void
     */
    public function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * @covers ::getLazyloadScript
     * @author Remy Perona
     */
    public function testShouldReturnLazyloadScriptWhenScriptDebug()
    {
        Functions\when('esc_attr')->returnArg();
        Functions\when('wp_parse_args')->alias(function ($args, $defaults) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                parse_str($args, $r);
            }
        
            if (is_array($defaults)) {
                return array_merge($defaults, $r);
            }

            return $r;
        });

        define('SCRIPT_DEBUG', true);
        $args = [
            'base_url' => 'http://example.org/',
            'version'  => '10.19',
            'fallback' => '8.17',
        ];

        $expected = '<script>(function(w, d){
            var b = d.getElementsByTagName("body")[0];
            var s = d.createElement("script"); s.async = true;
            s.src = !("IntersectionObserver" in w) ? "http://example.org/lazyload-8.17.js" : "http://example.org/lazyload-10.19.js";
            w.lazyLoadOptions = {
                elements_selector: "img,iframe",
                data_src: "lazy-src",
                data_srcset: "lazy-srcset",
                data_sizes: "lazy-sizes",
                skip_invisible: false,
                class_loading: "lazyloading",
                class_loaded: "lazyloaded",
                threshold: 300,
                callback_load: function(element) {
                    if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
                        if (element.classList.contains("lazyloaded") ) {
                            if (typeof window.jQuery != "undefined") {
                                if (jQuery.fn.fitVids) {
                                    jQuery(element).parent().fitVids();
                                }
                            }
                        }
                    }
                }
            }; // Your options here. See "recipes" for more information about async.
            b.appendChild(s);
        }(window, document));
        
        // Listen to the Initialized event
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            // Get the instance and puts it in the lazyLoadInstance variable
            var lazyLoadInstance = e.detail.instance;
        
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        lazyLoadInstance.update();
                    } );
                } );
                
                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };
                
                observer.observe(b, config);
            }
        }, false);
        </script>';

        $this->assertSame(
            $expected,
            $this->assets->getLazyloadScript($args)
        );
    }

    /**
     * @covers ::getLazyloadScript
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @author Remy Perona
     */
    public function testShouldReturnMinLazyloadScriptWhenNoScriptDebug()
    {
        Functions\when('esc_attr')->returnArg();
        Functions\when('wp_parse_args')->alias(function ($args, $defaults) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                parse_str($args, $r);
            }
        
            if (is_array($defaults)) {
                return array_merge($defaults, $r);
            }

            return $r;
        });

        $args = [
            'base_url' => 'http://example.org/',
            'version'  => '10.19',
            'fallback' => '8.17',
        ];

        $expected = '<script>(function(w, d){
            var b = d.getElementsByTagName("body")[0];
            var s = d.createElement("script"); s.async = true;
            s.src = !("IntersectionObserver" in w) ? "http://example.org/lazyload-8.17.min.js" : "http://example.org/lazyload-10.19.min.js";
            w.lazyLoadOptions = {
                elements_selector: "img,iframe",
                data_src: "lazy-src",
                data_srcset: "lazy-srcset",
                data_sizes: "lazy-sizes",
                skip_invisible: false,
                class_loading: "lazyloading",
                class_loaded: "lazyloaded",
                threshold: 300,
                callback_load: function(element) {
                    if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
                        if (element.classList.contains("lazyloaded") ) {
                            if (typeof window.jQuery != "undefined") {
                                if (jQuery.fn.fitVids) {
                                    jQuery(element).parent().fitVids();
                                }
                            }
                        }
                    }
                }
            }; // Your options here. See "recipes" for more information about async.
            b.appendChild(s);
        }(window, document));
        
        // Listen to the Initialized event
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            // Get the instance and puts it in the lazyLoadInstance variable
            var lazyLoadInstance = e.detail.instance;
        
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        lazyLoadInstance.update();
                    } );
                } );
                
                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };
                
                observer.observe(b, config);
            }
        }, false);
        </script>';

        $this->assertSame(
            $expected,
            $this->assets->getLazyloadScript($args)
        );
    }

    /**
     * @covers ::getYoutubeThumbnailScript
     * @dataProvider youtubeDataProvider
     * @author Remy Perona
     */
    public function testShouldReturnYoutubeThumbnailScript($args, $expected)
    {
        Functions\when('wp_parse_args')->alias(function ($args, $defaults) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                parse_str($args, $r);
            }
        
            if (is_array($defaults)) {
                return array_merge($defaults, $r);
            }

            return $r;
        });

        $this->assertSame(
            $expected,
            $this->assets->getYoutubeThumbnailScript($args)
        );
    }

    /**
     * Data Provider for testShouldReturnYoutubeThumbnailScript
     *
     * @author Remy Perona
     *
     * @return array
     */
    public function youtubeDataProvider()
    {
        return [
            [
                [],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'mqdefault',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/mqdefault.jpg\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'sddefault',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/sddefault.jpg\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'hqdefault',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'maxresdefault',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/maxresdefault.jpg\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'ultra',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
        ];
    }

    /**
     * @covers ::getYoutubeThumbnailCSS
     * @author Remy Perona
     */
    public function testShouldReturnYoutubeThumbnailCSS()
    {
        Functions\when('wp_parse_args')->alias(function ($args, $defaults) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                parse_str($args, $r);
            }
        
            if (is_array($defaults)) {
                return array_merge($defaults, $r);
            }

            return $r;
        });

        $args = [
            'base_url' => 'http://example.org/', 
        ];

        $expected = '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(http://example.org/img/youtube.png) no-repeat;cursor:pointer}.wp-has-aspect-ratio .rll-youtube-player{position:absolute;padding-bottom:0;width:100%;height:100%;top:0;bottom:0;left:0;right:0;';

        $this->assertSame(
            $expected,
            $this->assets->getYoutubeThumbnailCSS($args)
        );
    }

    /**
     * @covers ::getNoJSCSS
     * @author Remy Perona
     */
    public function testGetnojscssShouldReturnCss()
    {
        $this->assertSame(
            '.no-js .rll-youtube-player, .no-js [data-lazy-src]{display:none !important;}',
            $this->assets->getNoJSCSS()
        );
    }
}
