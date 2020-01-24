<?php

namespace RocketLazyload\Tests\Unit\Assets;

use Brain\Monkey\Functions;
use RocketLazyload\Assets;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Assets::getInlineLazyloadScript
 */
class Test_GetInlineLazyloadScript extends TestCase {
	private $assets;

	public function setUp() {
		parent::setUp();
		$this->assets = new Assets();
	}

	public function testShouldReturnInlineLazyloadScriptWithOptions() {
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

		$args = [
			'options' => [
				'callback_finish' => '()=>{console.log("Finish")}',
				'use_native'      => 'true',
				'bad_option'      => 'test',
			],
		];

		$expected = 'window.lazyLoadOptions = {
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
                },' . PHP_EOL . 'callback_finish: ()=>{console.log("Finish")},use_native: true};
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            var lazyLoadInstance = e.detail.instance;

            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    var image_count = 0;
                    var iframe_count = 0;
                    var rocketlazy_count = 0;

                    mutations.forEach(function(mutation) {
                        for (i = 0; i < mutation.addedNodes.length; i++) {
                            if (typeof mutation.addedNodes[i].getElementsByTagName !== \'function\') {
                                return;
                            }

                           if (typeof mutation.addedNodes[i].getElementsByClassName !== \'function\') {
                                return;
                            }

                            images = mutation.addedNodes[i].getElementsByTagName(\'img\');
                            is_image = mutation.addedNodes[i].tagName == "IMG";
                            iframes = mutation.addedNodes[i].getElementsByTagName(\'iframe\');
                            is_iframe = mutation.addedNodes[i].tagName == "IFRAME";
                            rocket_lazy = mutation.addedNodes[i].getElementsByClassName(\'rocket-lazyload\');

                            image_count += images.length;
			                iframe_count += iframes.length;
			                rocketlazy_count += rocket_lazy.length;

                            if(is_image){
                                image_count += 1;
                            }

                            if(is_iframe){
                                iframe_count += 1;
                            }
                        }
                    } );

                    if(image_count > 0 || iframe_count > 0 || rocketlazy_count > 0){
                        lazyLoadInstance.update();
                    }
                } );

                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };

                observer.observe(b, config);
            }
        }, false);';

		$this->assertSame(
			$expected,
			$this->assets->getInlineLazyloadScript( $args )
		);
	}
}
