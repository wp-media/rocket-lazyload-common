<?php
/**
 * Minimal, deterministic test double for the read-only surface of
 * WP_HTML_Tag_Processor that RocketLazyload\Image::crossCheckWithHtmlApi() uses
 * (next_tag(), get_attribute()). This is not vendored WordPress core -- it only
 * exists to drive specific agree/disagree scenarios in an isolated PHPUnit
 * process (@runInSeparateProcess), so the global class it declares never leaks
 * into the rest of the unit test suite.
 *
 * Configuration is via a plain function and $GLOBALS, not a static class
 * method/property, so calling code never performs a static access on the
 * stub. Declared in the global namespace on purpose: RocketLazyload\Image
 * checks for the real class via `class_exists( 'WP_HTML_Tag_Processor' )`, an
 * unqualified, global lookup.
 */

if ( ! function_exists( 'rll_html_api_stub_configure' ) ) {
	/**
	 * Configures how the next WP_HTML_Tag_Processor stub instance behaves.
	 *
	 * @param array<string, string|true> $attributes Values get_attribute() should return,
	 *                                                keyed by attribute name. A missing key
	 *                                                means "not present" (returns null).
	 * @param bool                       $has_tag    Whether next_tag() should report a match.
	 *
	 * @return void
	 */
	function rll_html_api_stub_configure( array $attributes, $has_tag = true ) {
		$GLOBALS['rll_html_api_stub_attributes'] = $attributes;
		$GLOBALS['rll_html_api_stub_has_tag']    = $has_tag;
	}
}

if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
	class WP_HTML_Tag_Processor {
		/**
		 * Kept for parity with the real class; this stub does not otherwise parse it.
		 *
		 * @var string
		 */
		private $html;

		/**
		 * @param string $html HTML fragment being processed.
		 */
		public function __construct( $html ) {
			$this->html = $html;
		}

		/**
		 * @return bool
		 */
		public function next_tag() {
			return $GLOBALS['rll_html_api_stub_has_tag'] ?? true;
		}

		/**
		 * @param string $name Attribute name.
		 *
		 * @return string|true|null
		 */
		public function get_attribute( $name ) {
			$attributes = $GLOBALS['rll_html_api_stub_attributes'] ?? [];

			return array_key_exists( $name, $attributes ) ? $attributes[ $name ] : null;
		}
	}
}
