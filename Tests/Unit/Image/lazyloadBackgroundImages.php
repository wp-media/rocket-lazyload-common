<?php

namespace RocketLazyload\Tests\Unit\Image;

use Brain\Monkey\Functions;
use RocketLazyload\Image;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Image::lazyloadBackgroundImages
 * @uses RocketLazyload\Image::addLazyClass
 * @uses RocketLazyload\Image::findRealAttribute
 * @uses RocketLazyload\Image::getAttributeQuotes
 * @uses RocketLazyload\Image::getClasses
 * @uses RocketLazyload\Image::getExcludedAttributes
 * @uses RocketLazyload\Image::getExcludedSrc
 * @uses RocketLazyload\Image::isExcluded
 * @uses RocketLazyload\Image::isOffsetInsideQuotedValue
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
}
