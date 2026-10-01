<?php

namespace RocketLazyload\Tests\Unit\Image;

use Brain\Monkey\Functions;
use RocketLazyload\Image;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Image::lazyloadBackgroundImages
 * @uses RocketLazyload\Image::addLazyClass
 * @uses RocketLazyload\Image::advanceQuoteState
 * @uses RocketLazyload\Image::findRealAttribute
 * @uses RocketLazyload\Image::getAttributeQuotes
 * @uses RocketLazyload\Image::getClasses
 * @uses RocketLazyload\Image::getExcludedAttributes
 * @uses RocketLazyload\Image::getExcludedSrc
 * @uses RocketLazyload\Image::isExcluded
 * @uses RocketLazyload\Image::normalizeClasses
 * @uses RocketLazyload\Image::stringToArray
 * @uses RocketLazyload\Image::trimOuterQuotes
 * @group  Image
 */
class Test_LazyloadBackgroundImages extends TestCase {
	private $image;

	protected function set_up() {
		parent::set_up();
		$this->image = new Image();
	}

	public function testShouldReturnSameWhenNoBackgroundImage() {
		$noimage = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/noimage.html' );

		$this->assertSame(
			$noimage,
			$this->image->lazyloadBackgroundImages( $noimage, $noimage )
		);
	}

	public function testShouldReturnBackgroundImagesLazyloaded() {
		$this->stubEscapeFunctions();

		Functions\when( 'wp_strip_all_tags' )->alias( static function( $string, $remove_breaks = false ) {
			$string = \preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
			$string = \strip_tags( $string );

			if ( $remove_breaks ) {
				$string = \preg_replace( '/[\r\n\t ]+/', ' ', $string );
			}

			return \trim( $string );
		} );

		$original = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/bgimages.html' );
		$expected = file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/image/bgimageslazyloaded.html' );

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $original, $original )
		);
	}

	/**
	 * An attribute whose own text happens to contain the word "class" is not a
	 * real class attribute: the genuine, separate `class` attribute on the same
	 * tag must still be found and rewritten, and the other attribute's value
	 * must round-trip unchanged.
	 */
	public function testShouldKeepUnrelatedAttributeValueUnchangedWhenRealClassExists() {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$input    = '<div title="an ordinary tooltip" class="my-class" style="background-image:url(https://example.com/a.png)">bg</div>';
		$expected = '<div data-bg="https://example.com/a.png" title="an ordinary tooltip" class="my-class rocket-lazyload" style="">bg</div>';

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $input, $input )
		);
	}

	/**
	 * A tag with no real class attribute at all still gets a brand-new one,
	 * unaffected by this fix.
	 */
	public function testShouldAddNewClassWhenNoRealClassAttributeExists() {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$input    = '<a href="https://example.com/" style="background-image:url(https://example.com/a.png)">bg</a>';
		$expected = '<a data-bg="https://example.com/a.png" class="rocket-lazyload" href="https://example.com/" style="">bg</a>';

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $input, $input )
		);
	}

	/**
	 * A `class=`-shaped token nested inside another attribute's still-open value
	 * (here, right after the opening quote, which is the shape that a naive
	 * "count the quotes" check would miss) must never be treated as a real
	 * class attribute. The outer attribute's value must round-trip byte for
	 * byte, and no attribute derived from those tokens must appear.
	 */
	public function testShouldIgnoreClassShapedTokenNestedInsideAnotherAttributeValue() {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$input    = '<div title=" class=x marker=y flag=z" style="background-image:url(https://example.com/a.png)">bg</div>';
		$expected = '<div data-bg="https://example.com/a.png" class="rocket-lazyload" title=" class=x marker=y flag=z" style="">bg</div>';

		$actual = $this->image->lazyloadBackgroundImages( $input, $input );

		$this->assertSame( $expected, $actual );

		$tag = $this->parseFirstElementAttributes( $actual );

		$this->assertSame(
			[ 'data-bg', 'class', 'title', 'style' ],
			array_keys( $tag )
		);
		$this->assertSame( 'rocket-lazyload', $tag['class'] );
		$this->assertSame( ' class=x marker=y flag=z', $tag['title'] );
		$this->assertArrayNotHasKey( 'marker', $tag );
		$this->assertArrayNotHasKey( 'flag', $tag );
		$this->assertArrayNotHasKey( 'x', $tag );
	}

	/**
	 * A decoy `class=`-shaped token nested inside an earlier attribute's value
	 * must be ignored even when a real `class` attribute exists later on the
	 * same tag: the real one is the one that gets rewritten.
	 */
	public function testShouldUseRealClassAttributePlacedAfterDecoyToken() {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$input    = '<div title=" class=decoy" class="real" style="background-image:url(https://example.com/a.png)">bg</div>';
		$expected = '<div data-bg="https://example.com/a.png" title=" class=decoy" class="real rocket-lazyload" style="">bg</div>';

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $input, $input )
		);
	}

	/**
	 * Vector 1b, correctness-only coverage: a tag with a neutral `style=`-shaped
	 * token nested inside another attribute's value, and no genuine `style`
	 * attribute anywhere, must never be selected as a background-image
	 * candidate. Output is byte-identical to input.
	 */
	public function testShouldNotSelectTagWithoutGenuineStyleAttribute() {
		$input = '<a href="https://example.com/" title=" style=marker text">click</a>';

		$this->assertSame(
			$input,
			$this->image->lazyloadBackgroundImages( $input, $input )
		);
	}

	/**
	 * Regression control for the vector-1b restructuring: an `<a>` tag with a
	 * genuine `style` background-image attribute must still be lazyloaded
	 * correctly.
	 */
	public function testShouldStillLazyloadAnchorWithGenuineStyleAttribute() {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$input    = '<a href="https://example.com/" style="background-image:url(https://example.com/b.png)" title="click me">click</a>';
		$expected = '<a data-bg="https://example.com/b.png" class="rocket-lazyload" href="https://example.com/" style="" title="click me">click</a>';

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $input, $input )
		);
	}

	/**
	 * findRealAttribute() correctness matrix, exercised only through this
	 * public method: mixed quote types, an unquoted value, a value containing
	 * a slash, an uppercase attribute name, duplicate attributes (first wins),
	 * tabs/newlines around `=`, an entity-encoded quote inside a value, a
	 * valueless attribute before the real one, and a defensive, unbalanced-quote
	 * tag that must degrade safely (left untouched).
	 *
	 * @dataProvider findRealAttributeMatrixProvider
	 */
	public function testShouldFindRealAttributeAcrossQuoteAndSpacingVariants( $input, $expected ) {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $input, $input )
		);
	}

	public function findRealAttributeMatrixProvider() {
		return [
			'mixed quote types on the same tag'            => [
				'<div title=\'He said "hi"\' class="my-class" style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" title=\'He said "hi"\' class="my-class rocket-lazyload" style="">bg</div>',
			],
			'unquoted class value'                         => [
				'<div class=foo style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" class="foo rocket-lazyload" style="">bg</div>',
			],
			'class value containing a slash'               => [
				'<div class="my/class" style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" class="my/class rocket-lazyload" style="">bg</div>',
			],
			'uppercase attribute name'                     => [
				'<div CLASS="my-class" style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" class="my-class rocket-lazyload" style="">bg</div>',
			],
			'duplicate class attributes, first one wins'   => [
				'<div class="first" class="second" style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" class="first rocket-lazyload" class="second" style="">bg</div>',
			],
			'tabs between attribute name and ='            => [
				"<div class\t=\t\"my-class\" style=\"background-image:url(https://example.com/a.png)\">bg</div>",
				'<div data-bg="https://example.com/a.png" class="my-class rocket-lazyload" style="">bg</div>',
			],
			'newline between attribute name and ='         => [
				"<div class\n=\n\"my-class\" style=\"background-image:url(https://example.com/a.png)\">bg</div>",
				'<div data-bg="https://example.com/a.png" class="my-class rocket-lazyload" style="">bg</div>',
			],
			'entity-encoded quote inside another value'    => [
				'<div title="a &quot;quoted&quot; word" class="my-class" style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" title="a &quot;quoted&quot; word" class="my-class rocket-lazyload" style="">bg</div>',
			],
			'valueless attribute before the real one'      => [
				'<div disabled class="my-class" style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" disabled class="my-class rocket-lazyload" style="">bg</div>',
			],
			'unbalanced quote degrades safely, untouched'  => [
				'<div title=\'never closes class="my-class" style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div title=\'never closes class="my-class" style="background-image:url(https://example.com/a.png)">bg</div>',
			],
			'double-quoted title, nested single-quoted style= and class= tokens, untouched' => [
				'<a href="https://example.com" title="style=\'background-image:url(https://example.com/a.png);\' class=x marker=y flag=z">lorem</a>',
				'<a href="https://example.com" title="style=\'background-image:url(https://example.com/a.png);\' class=x marker=y flag=z">lorem</a>',
			],
			'single-quoted title, nested double-quoted style= and class= tokens, untouched' => [
				"<a href=\"https://example.com\" title='style=\"background-image:url(https://example.com/a.png);\" class=x marker=y flag=z'>lorem</a>",
				"<a href=\"https://example.com\" title='style=\"background-image:url(https://example.com/a.png);\" class=x marker=y flag=z'>lorem</a>",
			],
		];
	}

	/**
	 * A tag whose title attribute holds a very long run of `style="d" `-shaped
	 * tokens (none of them real) must still resolve in time roughly
	 * proportional to its length, not to length squared: the per-candidate
	 * quote-state walk must not re-scan the tag from the start for every
	 * candidate it rejects. Exercised through the public method with a
	 * generous time budget so it isn't flaky on CI, while still failing hard
	 * if quadratic behaviour is reintroduced.
	 */
	public function testShouldProcessLongAttributeValuesInLinearTime() {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$tokens = str_repeat( "style='d' ", 4000 ); // ~40KB of never-real tokens, nested in the double-quoted title.
		$tag    = '<div title="' . $tokens . '" style="background-image:url(https://example.com/a.png)">bg</div>';

		$start    = microtime( true );
		$single   = $this->image->lazyloadBackgroundImages( $tag, $tag );
		$single_s = microtime( true ) - $start;

		$this->assertNotSame( $tag, $single, 'the genuine style attribute must still be found and lazyloaded' );
		$this->assertStringContainsString( 'data-bg="https://example.com/a.png"', $single );
		$this->assertLessThan( 2.0, $single_s, 'a single ~40KB tag must resolve well within budget' );

		$buffer = str_repeat( $tag . "\n", 20 );

		$start      = microtime( true );
		$many       = $this->image->lazyloadBackgroundImages( $buffer, $buffer );
		$many_s     = microtime( true ) - $start;

		$this->assertSame( 20, substr_count( $many, 'data-bg="https://example.com/a.png"' ) );
		$this->assertLessThan( 2.0, $many_s, '20 such tags in one buffer must still resolve well within budget' );
	}

	/**
	 * A stray quote inside an earlier tag's attribute value (e.g. unescaped
	 * JSON written by a third-party plugin) must not pair with a quote found
	 * later in the document: the candidate match for that earlier tag must end
	 * at its own `>`, as in browsers, so the backgrounds that follow are still
	 * lazyloaded.
	 *
	 * @dataProvider strayQuoteProvider
	 */
	public function testShouldLazyloadBackgroundsFollowingATagWithAStrayQuote( $input, $expected ) {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $input, $input )
		);
	}

	public function strayQuoteProvider() {
		return [
			'stray quote, quote in later text'                  => [
				'<div data-cfg="a=" b">x</div><div class="bg" style="background-image: url(https://example.com/bg.jpg)">bg</div><p title="t">He said " ok ></p>',
				'<div data-cfg="a=" b">x</div><div data-bg="https://example.com/bg.jpg" class="bg rocket-lazyload" style="">bg</div><p title="t">He said " ok ></p>',
			],
			'unescaped JSON in a data attribute, several bgs' => [
				'<div class="ctf" data-ctfshortcode="{"0": "linktextcolor=" ", "1": "num=3"}"></div>'
				. '<section class="s1" style="background-image:url(https://example.com/a.png)">a</section>'
				. '<div class="s2" data-x="1" style="background-image:url(https://example.com/b.png)">b</div>'
				. '<p>it\'s "quoted" text</p>',
				'<div class="ctf" data-ctfshortcode="{"0": "linktextcolor=" ", "1": "num=3"}"></div>'
				. '<section data-bg="https://example.com/a.png" class="s1 rocket-lazyload" style="">a</section>'
				. '<div data-bg="https://example.com/b.png" class="s2 rocket-lazyload" data-x="1" style="">b</div>'
				. '<p>it\'s "quoted" text</p>',
			],
		];
	}

	/**
	 * Parses the attribute list of the first HTML element found in $html.
	 *
	 * @param string $html HTML fragment containing exactly one element.
	 *
	 * @return array<string, string> Attribute name => value, in document order.
	 */
	private function parseFirstElementAttributes( $html ) {
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
		libxml_clear_errors();

		$element    = $dom->getElementsByTagName( '*' )->item( 2 ); // 0 is <html>, 1 is <body>, 2 is our element.
		$attributes = [];

		foreach ( $element->attributes as $attribute ) {
			$attributes[ $attribute->nodeName ] = $attribute->nodeValue;
		}

		return $attributes;
	}

	/**
	 * Browsers accept an attribute right after the closing quote of the previous
	 * value (`href="#"style=…`), and treat a quote that does not follow `=` as a
	 * plain character, not as the start of a value. Both shapes must be detected,
	 * while a `style=`/`class=` token inside another attribute's value must still
	 * be ignored.
	 *
	 * @dataProvider attributeAfterQuoteProvider
	 */
	public function testShouldDetectAttributesFollowingAQuote( $input, $expected ) {
		$this->stubEscapeFunctions();
		Functions\stubs( [ 'wp_strip_all_tags' ] );

		$this->assertSame(
			$expected,
			$this->image->lazyloadBackgroundImages( $input, $input )
		);
	}

	public function attributeAfterQuoteProvider() {
		return [
			'style right after a closing double quote'                => [
				'<a href="#"style="background-image:url(https://example.com/a.png)">bg</a>',
				'<a data-bg="https://example.com/a.png" class="rocket-lazyload" href="#"style="">bg</a>',
			],
			'class right after a closing double quote'                => [
				'<div id="x"class="a" style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" id="x"class="a rocket-lazyload" style="">bg</div>',
			],
			'stray quote before the style attribute'                  => [
				'<div class="a" " style="background-image:url(https://example.com/a.png)">bg</div>',
				'<div data-bg="https://example.com/a.png" class="a rocket-lazyload" " style="">bg</div>',
			],
			'style= after a double quote inside a single-quoted value' => [
				'<a title=\'x"style="background-image:url(https://example.com/a.png)"\'>lorem</a>',
				'<a title=\'x"style="background-image:url(https://example.com/a.png)"\'>lorem</a>',
			],
			'style= after a single quote inside a double-quoted value' => [
				'<a title="x\'style=\'background-image:url(https://example.com/a.png)\'">lorem</a>',
				'<a title="x\'style=\'background-image:url(https://example.com/a.png)\'">lorem</a>',
			],
			'stray quote, then style= inside a real value'            => [
				'<div " title="style=background-image:url(https://example.com/a.png)">lorem</div>',
				'<div " title="style=background-image:url(https://example.com/a.png)">lorem</div>',
			],
		];
	}
}
