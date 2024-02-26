<?php

namespace RocketLazyload\Tests\Unit\Assets;

use Brain\Monkey\Functions;
use Brain\Monkey\Filters;
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
	public function testShouldReturnYoutubeThumbnailScript( $args, $excluded, $expected ) {
		Filters\expectApplied( 'rocket_lazyload_exclude_youtube_thumbnail' )
			->andReturn( $excluded );

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
				[],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">', '[]', 'https://i.ytimg.com/vi/ID/hqdefault.jpg' ),
			],
			[
				[
					'resolution' => 'mqdefault',
				],
				[],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/mqdefault.jpg" alt="" width="320" height="180">', '[]', 'https://i.ytimg.com/vi/ID/mqdefault.jpg' ),
			],
			[
				[
					'resolution' => 'sddefault',
				],
				[],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/sddefault.jpg" alt="" width="640" height="480">', '[]', 'https://i.ytimg.com/vi/ID/sddefault.jpg' ),
			],
			[
				[
					'resolution' => 'hqdefault',
				],
				[],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">', '[]', 'https://i.ytimg.com/vi/ID/hqdefault.jpg' ),
			],
			[
				[
					'resolution' => 'maxresdefault',
				],
				[],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/maxresdefault.jpg" alt="" width="1280" height="720">', '[]', 'https://i.ytimg.com/vi/ID/maxresdefault.jpg' ),
			],
			[
				[
					'resolution' => 'ultra',
				],
				[],
				$this->add_element( '<img src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">', '[]', 'https://i.ytimg.com/vi/ID/hqdefault.jpg' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => false,
				],
				[],
				$this->add_element( '<img data-lazy-src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360"><noscript><img src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360"></noscript>', '[]', 'https://i.ytimg.com/vi/ID/hqdefault.jpg' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
				],
				[],
				$this->add_element( '<img loading="lazy" src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">', '[]', 'https://i.ytimg.com/vi/ID/hqdefault.jpg' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
					'extension'     => 'webp',
				],
				[],
				$this->add_element( '<img loading="lazy" src="https://i.ytimg.com/vi_webp/ID/hqdefault.webp" alt="" width="480" height="360">', '[]', 'https://i.ytimg.com/vi_webp/ID/hqdefault.webp' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
				],
				[
					'https://i.ytimg.com/vi/12345/hqdefault.jpg',
				],
				$this->add_element( '<img loading="lazy" src="https://i.ytimg.com/vi/ID/hqdefault.jpg" alt="" width="480" height="360">', '["https:\/\/i.ytimg.com\/vi\/12345\/hqdefault.jpg"]', 'https://i.ytimg.com/vi/ID/hqdefault.jpg' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
					'extension'     => 'webp',
				],
				[],
				"<script>function lazyLoadThumb(e,alt,l){var t='<img loading=\"lazy\" src=\"https://i.ytimg.com/vi_webp/ID/hqdefault.webp\" alt=\"\" width=\"480\" height=\"360\">',a='<button class=\"play\" aria-label=\"play Youtube video\"></button>';if(l){t=t.replace('data-lazy-','');t=t.replace('loading=\"lazy\"','');t=t.replace(/<noscript>.*?<\/noscript>/g,'');}t=t.replace('alt=\"\"','alt=\"'+alt+'\"');return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.parentNode.dataset.query.length?\"\":\"&\"+this.parentNode.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.parentNode.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\",\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.parentNode.replaceChild(e,this.parentNode)}document.addEventListener(\"DOMContentLoaded\",function(){var exclusions=[];var e,t,p,u,l,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)(e=document.createElement(\"div\")),(u='https://i.ytimg.com/vi_webp/ID/hqdefault.webp'),(u=u.replace('ID',a[t].dataset.id)),(l=exclusions.some(exclusion=>u.includes(exclusion))),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\",a[t].dataset.query),e.setAttribute(\"data-src\",a[t].dataset.src),(e.innerHTML=lazyLoadThumb(a[t].dataset.id,a[t].dataset.alt,l)),a[t].appendChild(e),(p=e.querySelector(\".play\")),(p.onclick=lazyLoadYoutubeIframe)});</script>"
			],
		];
	}

	private function add_element( $element, $excluded_patterns, $image_url ) {
		return "<script>function lazyLoadThumb(e,alt,l){var t='{$element}',a='<button class=\"play\" aria-label=\"play Youtube video\"></button>';if(l){t=t.replace('data-lazy-','');t=t.replace('loading=\"lazy\"','');t=t.replace(/<noscript>.*?<\/noscript>/g,'');}t=t.replace('alt=\"\"','alt=\"'+alt+'\"');return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.parentNode.dataset.query.length?\"\":\"&\"+this.parentNode.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.parentNode.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\",\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.parentNode.replaceChild(e,this.parentNode)}document.addEventListener(\"DOMContentLoaded\",function(){var exclusions={$excluded_patterns};var e,t,p,u,l,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)(e=document.createElement(\"div\")),(u='{$image_url}'),(u=u.replace('ID',a[t].dataset.id)),(l=exclusions.some(exclusion=>u.includes(exclusion))),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\",a[t].dataset.query),e.setAttribute(\"data-src\",a[t].dataset.src),(e.innerHTML=lazyLoadThumb(a[t].dataset.id,a[t].dataset.alt,l)),a[t].appendChild(e),(p=e.querySelector(\".play\")),(p.onclick=lazyLoadYoutubeIframe)});</script>";
	}
}
