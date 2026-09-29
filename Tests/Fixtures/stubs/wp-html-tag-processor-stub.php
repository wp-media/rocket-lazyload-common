<?php
/**
 * Minimal, deterministic test double for the read-only surface of
 * WP_HTML_Tag_Processor that RocketLazyload\Image::crossCheckWithHtmlApi() uses
 * (next_tag(), get_attribute()). This is not vendored WordPress core -- it only
 * exists to drive specific agree/disagree scenarios in an isolated PHPUnit
 * process (@runInSeparateProcess), so the global class it declares never leaks
 * into the rest of the unit test suite.
 *
 * Declared in the global namespace on purpose: RocketLazyload\Image checks for
 * it via `class_exists( 'WP_HTML_Tag_Processor' )`, an unqualified, global
 * lookup, exactly as it would for the real WordPress 6.2+ class.
 */

if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
	class WP_HTML_Tag_Processor {
		/**
		 * Whether next_tag() should report a matched tag opener.
		 *
		 * @var bool
		 */
		public static $has_tag = true;

		/**
		 * Attribute values get_attribute() should return, keyed by attribute name.
		 * A missing key means "attribute not present" (get_attribute() returns null).
		 *
		 * @var array<string, string|true>
		 */
		public static $attributes = [];

		/**
		 * @param string $html Unused by this stub; kept for signature compatibility.
		 */
		public function __construct( $html ) {}

		/**
		 * @return bool
		 */
		public function next_tag() {
			return self::$has_tag;
		}

		/**
		 * @param string $name Attribute name.
		 *
		 * @return string|true|null
		 */
		public function get_attribute( $name ) {
			return array_key_exists( $name, self::$attributes ) ? self::$attributes[ $name ] : null;
		}

		/**
		 * Resets the stub between scenarios within the same isolated process.
		 *
		 * @return void
		 */
		public static function reset_stub() {
			self::$has_tag   = true;
			self::$attributes = [];
		}
	}
}
