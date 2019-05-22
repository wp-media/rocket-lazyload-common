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
        parent::setUp();
        Monkey\setUp();
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
            'version'  => '11.0.2',
        ];

        $expected = '<script>
            window.lazyLoadOptions = {
                elements_selector: "img,iframe",
                data_src: "lazy-src",
                data_srcset: "lazy-srcset",
                data_sizes: "lazy-sizes",
                class_loading: "lazyloading",
                class_loaded: "lazyloaded",
                threshold: 300,
                callback_loaded: function(element) {
                    if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
                        if (element.classList.contains("lazyloaded") ) {
                            if (typeof window.jQuery != "undefined") {
                                if (jQuery.fn.fitVids) {
                                    jQuery(element).parent().fitVids();
                                }
                            }
                        }
                    }
                }};
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            var lazyLoadInstance = e.detail.instance;
        
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        for (i = 0; i < mutation.addedNodes.length; i++) {
                            if (typeof mutation.addedNodes[i].getElementsByTagName !== \'function\') {
                                return;
                            }

                           if (typeof mutation.addedNodes[i].getElementsByClassName !== \'function\') {
                                return;
                            }

                            imgs = mutation.addedNodes[i].getElementsByTagName(\'img\');
                            iframes = mutation.addedNodes[i].getElementsByTagName(\'iframe\');
                            rocket_lazy = mutation.addedNodes[i].getElementsByClassName(\'rocket-lazyload\');

                            if ( 0 === imgs.length && 0 === iframes.length && 0 === rocket_lazy.length ) {
                                return;
                            }

                            lazyLoadInstance.update();
                        }
                    } );
                } );
                
                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };
                
                observer.observe(b, config);
            }
        }, false);
        </script><script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.js"></script>';

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

        define('SCRIPT_DEBUG', false);

        $args = [
            'base_url' => 'http://example.org/',
            'version'  => '11.0.2',
        ];

        $expected = '<script>
            window.lazyLoadOptions = {
                elements_selector: "img,iframe",
                data_src: "lazy-src",
                data_srcset: "lazy-srcset",
                data_sizes: "lazy-sizes",
                class_loading: "lazyloading",
                class_loaded: "lazyloaded",
                threshold: 300,
                callback_loaded: function(element) {
                    if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
                        if (element.classList.contains("lazyloaded") ) {
                            if (typeof window.jQuery != "undefined") {
                                if (jQuery.fn.fitVids) {
                                    jQuery(element).parent().fitVids();
                                }
                            }
                        }
                    }
                }};
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            var lazyLoadInstance = e.detail.instance;
        
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        for (i = 0; i < mutation.addedNodes.length; i++) {
                            if (typeof mutation.addedNodes[i].getElementsByTagName !== \'function\') {
                                return;
                            }

                           if (typeof mutation.addedNodes[i].getElementsByClassName !== \'function\') {
                                return;
                            }

                            imgs = mutation.addedNodes[i].getElementsByTagName(\'img\');
                            iframes = mutation.addedNodes[i].getElementsByTagName(\'iframe\');
                            rocket_lazy = mutation.addedNodes[i].getElementsByClassName(\'rocket-lazyload\');

                            if ( 0 === imgs.length && 0 === iframes.length && 0 === rocket_lazy.length ) {
                                return;
                            }

                            lazyLoadInstance.update();
                        }
                    } );
                } );
                
                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };
                
                observer.observe(b, config);
            }
        }, false);
        </script><script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.min.js"></script>';

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
    public function testShouldReturnLazyloadScriptWithPolyfill()
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

        define('SCRIPT_DEBUG', false);

        $args = [
            'base_url' => 'http://example.org/',
            'version'  => '11.0.2',
            'polyfill' => true,
        ];

        $expected = '<script crossorigin="anonymous" src="https://polyfill.io/v3/polyfill.min.js?flags=gated&features=default%2CIntersectionObserver%2CIntersectionObserverEntry"></script><script>
            window.lazyLoadOptions = {
                elements_selector: "img,iframe",
                data_src: "lazy-src",
                data_srcset: "lazy-srcset",
                data_sizes: "lazy-sizes",
                class_loading: "lazyloading",
                class_loaded: "lazyloaded",
                threshold: 300,
                callback_loaded: function(element) {
                    if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
                        if (element.classList.contains("lazyloaded") ) {
                            if (typeof window.jQuery != "undefined") {
                                if (jQuery.fn.fitVids) {
                                    jQuery(element).parent().fitVids();
                                }
                            }
                        }
                    }
                }};
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            var lazyLoadInstance = e.detail.instance;
        
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        for (i = 0; i < mutation.addedNodes.length; i++) {
                            if (typeof mutation.addedNodes[i].getElementsByTagName !== \'function\') {
                                return;
                            }

                           if (typeof mutation.addedNodes[i].getElementsByClassName !== \'function\') {
                                return;
                            }

                            imgs = mutation.addedNodes[i].getElementsByTagName(\'img\');
                            iframes = mutation.addedNodes[i].getElementsByTagName(\'iframe\');
                            rocket_lazy = mutation.addedNodes[i].getElementsByClassName(\'rocket-lazyload\');

                            if ( 0 === imgs.length && 0 === iframes.length && 0 === rocket_lazy.length ) {
                                return;
                            }

                            lazyLoadInstance.update();
                        }
                    } );
                } );
                
                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };
                
                observer.observe(b, config);
            }
        }, false);
        </script><script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.min.js"></script>';

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
    public function testShouldReturnLazyloadScriptWithOptions()
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

        define('SCRIPT_DEBUG', false);

        $args = [
            'base_url' => 'http://example.org/',
            'version'  => '11.0.2',
            'polyfill' => false,
            'options'  => [
                'callback_finish' => '()=>{console.log("Finish")}',
                'use_native'      => 'true',
                'bad_option'      => 'test',
            ],
        ];

        $expected = '<script>
            window.lazyLoadOptions = {
                elements_selector: "img,iframe",
                data_src: "lazy-src",
                data_srcset: "lazy-srcset",
                data_sizes: "lazy-sizes",
                class_loading: "lazyloading",
                class_loaded: "lazyloaded",
                threshold: 300,
                callback_loaded: function(element) {
                    if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
                        if (element.classList.contains("lazyloaded") ) {
                            if (typeof window.jQuery != "undefined") {
                                if (jQuery.fn.fitVids) {
                                    jQuery(element).parent().fitVids();
                                }
                            }
                        }
                    }
                },
callback_finish: ()=>{console.log("Finish")},use_native: true};
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            var lazyLoadInstance = e.detail.instance;
        
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        for (i = 0; i < mutation.addedNodes.length; i++) {
                            if (typeof mutation.addedNodes[i].getElementsByTagName !== \'function\') {
                                return;
                            }

                           if (typeof mutation.addedNodes[i].getElementsByClassName !== \'function\') {
                                return;
                            }

                            imgs = mutation.addedNodes[i].getElementsByTagName(\'img\');
                            iframes = mutation.addedNodes[i].getElementsByTagName(\'iframe\');
                            rocket_lazy = mutation.addedNodes[i].getElementsByClassName(\'rocket-lazyload\');

                            if ( 0 === imgs.length && 0 === iframes.length && 0 === rocket_lazy.length ) {
                                return;
                            }

                            lazyLoadInstance.update();
                        }
                    } );
                } );
                
                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };
                
                observer.observe(b, config);
            }
        }, false);
        </script><script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.min.js"></script>';

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
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" width=\"480\" height=\"360\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'mqdefault',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/mqdefault.jpg\" width=\"320\" height=\"180\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'sddefault',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/sddefault.jpg\" width=\"640\" height=\"480\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'hqdefault',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" width=\"480\" height=\"360\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'maxresdefault',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/maxresdefault.jpg\" width=\"1280\" height=\"720\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'ultra',
                ],
                "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" width=\"480\" height=\"360\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
            [
                [
                    'resolution' => 'hqdefault',
                    'lazy_image' => true,
                ],
                "<script>function lazyLoadThumb(e){var t='<img data-lazy-src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" width=\"480\" height=\"360\"><noscript><img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" width=\"480\" height=\"360\"></noscript>',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
            ],
        ];
    }

    /**
     * @covers ::getYoutubeThumbnailCSS
     * @author Remy Perona
     */
    public function testShouldReturnYoutubeThumbnailCSSWithResponsive()
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

        $expected = '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(http://example.org/img/youtube.png) no-repeat;cursor:pointer}.wp-has-aspect-ratio .rll-youtube-player{position:absolute;padding-bottom:0;width:100%;height:100%;top:0;bottom:0;left:0;right:0}';

        $this->assertSame(
            $expected,
            $this->assets->getYoutubeThumbnailCSS($args)
        );
    }

    /**
     * @covers ::getYoutubeThumbnailCSS
     * @author Remy Perona
     */
    public function testShouldReturnYoutubeThumbnailCSSWithoutResponsive()
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
            'base_url'          => 'http://example.org/',
            'responsive_embeds' => false,
        ];

        $expected = '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(http://example.org/img/youtube.png) no-repeat;cursor:pointer}';

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
            '<noscript><style id="rocket-lazyload-nojs-css">.rll-youtube-player, [data-lazy-src]{display:none !important;}</style></noscript>',
            $this->assets->getNoJSCSS()
        );
    }
}
