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
				"<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" alt=\"\" width=\"480\" height=\"360\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
			],
			[
				[
					'resolution' => 'mqdefault',
				],
				"<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/mqdefault.jpg\" alt=\"\" width=\"320\" height=\"180\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
			],
			[
				[
					'resolution' => 'sddefault',
				],
				"<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/sddefault.jpg\" alt=\"\" width=\"640\" height=\"480\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
			],
			[
				[
					'resolution' => 'hqdefault',
				],
				"<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" alt=\"\" width=\"480\" height=\"360\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
			],
			[
				[
					'resolution' => 'maxresdefault',
				],
				"<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/maxresdefault.jpg\" alt=\"\" width=\"1280\" height=\"720\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
			],
			[
				[
					'resolution' => 'ultra',
				],
				"<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" alt=\"\" width=\"480\" height=\"360\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => false,
				],
				"<script>function lazyLoadThumb(e){var t='<img data-lazy-src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" alt=\"\" width=\"480\" height=\"360\"><noscript><img src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" alt=\"\" width=\"480\" height=\"360\"></noscript>',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
				],
				"<script>function lazyLoadThumb(e){var t='<img loading=\"lazy\" src=\"https://i.ytimg.com/vi/ID/hqdefault.jpg\" alt=\"\" width=\"480\" height=\"360\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.src)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),e.setAttribute(\"allow\", \"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.setAttribute(\"data-src\", a[t].dataset.src),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>",
			],
		];
	}
}
