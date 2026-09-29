<?php

namespace RocketLazyload\Tests\Unit\Assets;

use Brain\Monkey\Functions;
use Brain\Monkey\Filters;
use RocketLazyload\Assets;
use RocketLazyload\RenderToken;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Assets::getYoutubeThumbnailScript
 * @uses RocketLazyload\RenderToken::get
 */
class Test_GetYoutubeThumbnaiScript extends TestCase {
	private $assets;

	protected function set_up() {
		parent::set_up();
		$this->assets = new Assets();

		// Deterministic token so the expected script text is exact and stable.
		RenderToken::reset( '0123456789abcdef' );

		Functions\when( 'wp_parse_args' )->alias( static function ( $parsed_args, $defaults ) {
			return \array_merge( $defaults, $parsed_args );
		} );
	}

	protected function tear_down() {
		RenderToken::reset();
		parent::tear_down();
	}

	/**
	 * @dataProvider youtubeDataProvider
	 *
	 * @param array  $args     An array of arguments to configure the inline script.
	 * @param string $excluded the excluded patterns returned by the filter.
	 * @param string $expected the expected HTML.
	 */
	public function testShouldReturnYoutubeThumbnailScript( $args, $excluded, $expected ) {
		Filters\expectApplied( 'rocket_lazyload_exclude_youtube_thumbnail' )
			->andReturn( $excluded );

		$actual = $this->assets->getYoutubeThumbnailScript( $args );

		$this->assertSame( $expected, $actual );

		// The generated script must never build markup from element data via
		// innerHTML, and must validate the id and embed URL before using them.
		$this->assertStringNotContainsString( 'innerHTML', $actual );
		$this->assertStringContainsString( '/^[A-Za-z0-9_-]{11}$/', $actual );
		$this->assertStringContainsString( '/^https:\/\/(www\.)?youtube(-nocookie)?\.com\/embed\/[A-Za-z0-9_-]{11}$/', $actual );
		$this->assertStringContainsString( 'new URL(', $actual );
		$this->assertStringContainsString( 'new URLSearchParams(', $actual );

		// The script must only ever process elements carrying this request's
		// own render token, and skip everything else.
		$this->assertStringContainsString( 'var token="0123456789abcdef";', $actual );
		$this->assertStringContainsString( 'if(a[t].dataset.rllToken!==token){continue;}', $actual );
	}

	/**
	 * A play-button label containing a quote, a backslash, and a literal
	 * `</script>` must be embedded as an inert JS string, unable to end the
	 * surrounding <script> tag or break out of the string literal.
	 */
	public function testShouldSafelyEscapeAriaLabelContainingSpecialCharacters() {
		Filters\expectApplied( 'rocket_lazyload_exclude_youtube_thumbnail' )
			->andReturn( [] );

		$label = 'play "cool" video\\</script>';

		$actual = $this->assets->getYoutubeThumbnailScript( [ 'button_aria_label' => $label ] );

		$this->assertStringNotContainsString( '</script>', substr( $actual, 8, -9 ), 'the label must not contain a literal closing script tag inside the script body' );
		$this->assertStringContainsString(
			'btn.setAttribute("aria-label",' . wp_json_encode( $label ) . ');',
			$actual
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
				$this->build_script( 'https://i.ytimg.com/vi/ID/hqdefault.jpg', 480, 360, 'true', 'false', 'play Youtube video', '[]' ),
			],
			[
				[
					'resolution' => 'mqdefault',
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi/ID/mqdefault.jpg', 320, 180, 'true', 'false', 'play Youtube video', '[]' ),
			],
			[
				[
					'resolution' => 'sddefault',
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi/ID/sddefault.jpg', 640, 480, 'true', 'false', 'play Youtube video', '[]' ),
			],
			[
				[
					'resolution' => 'hqdefault',
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi/ID/hqdefault.jpg', 480, 360, 'true', 'false', 'play Youtube video', '[]' ),
			],
			[
				[
					'resolution' => 'maxresdefault',
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi/ID/maxresdefault.jpg', 1280, 720, 'true', 'false', 'play Youtube video', '[]' ),
			],
			[
				[
					'resolution' => 'ultra',
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi/ID/hqdefault.jpg', 480, 360, 'true', 'false', 'play Youtube video', '[]' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => false,
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi/ID/hqdefault.jpg', 480, 360, 'false', 'true', 'play Youtube video', '[]' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi/ID/hqdefault.jpg', 480, 360, 'true', 'true', 'play Youtube video', '[]' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
					'extension'  => 'webp',
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi_webp/ID/hqdefault.webp', 480, 360, 'true', 'true', 'play Youtube video', '[]' ),
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
				$this->build_script( 'https://i.ytimg.com/vi/ID/hqdefault.jpg', 480, 360, 'true', 'true', 'play Youtube video', '["https:\/\/i.ytimg.com\/vi\/12345\/hqdefault.jpg"]' ),
			],
			[
				[
					'resolution' => 'hqdefault',
					'lazy_image' => true,
					'native'     => true,
					'extension'  => 'webp',
				],
				[],
				$this->build_script( 'https://i.ytimg.com/vi_webp/ID/hqdefault.webp', 480, 360, 'true', 'true', 'play Youtube video', '[]' ),
			],
		];
	}

	/**
	 * Builds the expected inline script from its variable parts.
	 *
	 * @param string $image_url         Thumbnail image URL, still containing the `ID` placeholder.
	 * @param int    $width             Thumbnail width.
	 * @param int    $height            Thumbnail height.
	 * @param string $native            `'true'` or `'false'` (as a JS literal, not a PHP bool).
	 * @param string $lazy_image        `'true'` or `'false'` (as a JS literal, not a PHP bool).
	 * @param string $button_aria_label Play button aria-label text.
	 * @param string $excluded_patterns JSON-encoded array of excluded patterns.
	 *
	 * @return string
	 */
	private function build_script( $image_url, $width, $height, $native, $lazy_image, $button_aria_label, $excluded_patterns ) {
		return '<script>'
			. 'function lazyLoadImg(id,l){'
			. 'var s=\'' . $image_url . '\'.replace("ID",id),img=document.createElement("img");'
			. 'if(' . $lazy_image . '&&!l&&' . $native . '){img.setAttribute("loading","lazy");img.setAttribute("src",s);}'
			. 'else if(' . $lazy_image . '&&!l&&!' . $native . '){img.setAttribute("data-lazy-src",s);}'
			. 'else{img.setAttribute("src",s);}'
			. 'img.setAttribute("width","' . $width . '");'
			. 'img.setAttribute("height","' . $height . '");'
			. 'return img;'
			. '}'
			. 'function lazyLoadThumb(id,alt,l){'
			. 'if(!/^[A-Za-z0-9_-]{11}$/.test(id)){return null;}'
			. 'var frag=document.createDocumentFragment(),img=lazyLoadImg(id,l);'
			. 'img.setAttribute("alt",alt);'
			. 'frag.appendChild(img);'
			. 'if(' . $lazy_image . '&&!' . $native . '&&!l){'
			. 'var noscript=document.createElement("noscript"),nimg=lazyLoadImg(id,true);'
			. 'nimg.setAttribute("alt",alt);'
			. 'noscript.appendChild(nimg);'
			. 'frag.appendChild(noscript);'
			. '}'
			. 'var btn=document.createElement("button");'
			. 'btn.setAttribute("class","play");'
			. 'btn.setAttribute("aria-label",' . json_encode( $button_aria_label ) . ');'
			. 'frag.appendChild(btn);'
			. 'return frag;'
			. '}'
			. 'function lazyLoadYoutubeIframe(){'
			. 'var src=this.parentNode.dataset.src;'
			. 'if(!/^https:\/\/(www\.)?youtube(-nocookie)?\.com\/embed\/[A-Za-z0-9_-]{11}$/.test(src)){return;}'
			. 'var url=new URL(src);'
			. 'url.searchParams.set("autoplay","1");'
			. 'var query=this.parentNode.dataset.query||"";'
			. 'new URLSearchParams(query).forEach(function(v,k){url.searchParams.set(k,v);});'
			. 'var e=document.createElement("iframe");'
			. 'e.setAttribute("src",url.href);'
			. 'e.setAttribute("frameborder","0");'
			. 'e.setAttribute("allowfullscreen","1");'
			. 'e.setAttribute("allow","accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture");'
			. 'this.parentNode.parentNode.replaceChild(e,this.parentNode);'
			. '}'
			. 'document.addEventListener("DOMContentLoaded",function(){'
			. 'var token="0123456789abcdef";'
			. 'var exclusions=' . $excluded_patterns . ';'
			. 'var e,t,frag,u,l,a=document.getElementsByClassName("rll-youtube-player");'
			. 'for(t=0;t<a.length;t++){'
			. 'if(a[t].dataset.rllToken!==token){continue;}'
			. 'u=\'' . $image_url . '\'.replace("ID",a[t].dataset.id);'
			. 'l=exclusions.some(function(exclusion){return u.indexOf(exclusion)!==-1;});'
			. 'e=document.createElement("div");'
			. 'e.setAttribute("data-id",a[t].dataset.id);'
			. 'e.setAttribute("data-query",a[t].dataset.query);'
			. 'e.setAttribute("data-src",a[t].dataset.src);'
			. 'frag=lazyLoadThumb(a[t].dataset.id,a[t].dataset.alt,l);'
			. 'if(!frag){continue;}'
			. 'e.appendChild(frag);'
			. 'a[t].appendChild(e);'
			. 'e.querySelector(".play").onclick=lazyLoadYoutubeIframe;'
			. '}'
			. '});'
			. '</script>';
	}
}
