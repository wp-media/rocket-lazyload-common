<?php

namespace RocketLazyload\Tests\Unit\Assets;

use Brain\Monkey\Functions;
use RocketLazyload\Assets;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Assets::getYoutubeThumbnailScript
 */
class Test_GetYoutubeThumbnaiScript extends TestCase {
	private $assets;

	protected function set_up() {
		parent::set_up();
		$this->assets = new Assets();

		Functions\when( 'wp_parse_args' )->alias( static function ( $parsed_args, $defaults ) {
			return \array_merge( $defaults, $parsed_args );
		} );
	}

	/**
	 * @dataProvider youtubeDataProvider
	 *
	 * @param array  $args     An array of arguments to configure the inline script.
	 * @param string $expected the expected HTML.
	 */
	public function testShouldReturnYoutubeThumbnailScript( $args, $expected ) {
		$this->assertSame(
			$expected,
			$this->assets->getYoutubeThumbnailScript( $args )
		);
	}

	/**
	 * Data Provider for testShouldReturnYoutubeThumbnailScript.
	 *
	 * @return array
	 */
	public function youtubeDataProvider() {
		return [
			[
				[],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">' ),
			],
			[
				[
					'resolution' => 'mqdefault',
				],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/mqdefault.jpg" alt="" width="320" height="180">' ),
			],
			[
				[
					'resolution' => 'sddefault',
				],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/sddefault.jpg" alt="" width="640" height="480">' ),
			],
			[
				[
					'resolution' => 'hqdefault',
				],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">' ),
			],
			[
				[
					'resolution' => 'maxresdefault',
				],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/maxresdefault.jpg" alt="" width="1280" height="720">' ),
			],
			[
				[
					'resolution' => 'ultra',
				],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => false,
				],
				$this->add_element( '<img data-lazy-src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360"><noscript><img src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360"></noscript>' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
				],
				$this->add_element( '<img loading="lazy" src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
					'extension'     => 'webp',
				],
				$this->add_element( '<img loading="lazy" src="https://i.ytimg.com/vi/ID/hqdefault.webp" alt="" width="480" height="360">' ),
			],
		];
	}

	private function add_element( $element ) {
		return "<script>function lazyLoadThumb(e,alt){var t='{$element}',a='<button class=\"play\" aria-label=\"play Youtube video\"></button>';t=t.replace('alt=\"\"','alt=\"'+alt+'\"');return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.parentNode.dataset.query.length?'':'&'+this.parentNode.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.parentNode.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.parentNode.replaceChild(e,this.parentNode)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,p,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id,a[t].dataset.alt),a[t].appendChild(e),p=e.querySelector('.play'),p.onclick=lazyLoadYoutubeIframe});</script>";
	}
}
