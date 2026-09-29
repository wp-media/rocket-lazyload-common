<?php
declare(strict_types=1);

/**
 * Provides a single, per-request token shared by every renderer in this library.
 *
 * @package RocketLazyload
 */

namespace RocketLazyload;

/**
 * A per-request random token, read by Assets and written by Iframe (and any other
 * renderer that needs it), so the client-side script can recognise markup this
 * library rendered in the same request/response without relying on a static,
 * guessable marker.
 */
class RenderToken {

	/**
	 * Current request's token, generated lazily on first use.
	 *
	 * @var string|null
	 */
	private static $token;

	/**
	 * Gets the current request's render token, generating one on first use.
	 *
	 * @return string
	 */
	public static function get() {
		if ( null === self::$token ) {
			self::$token = bin2hex( random_bytes( 8 ) );
		}

		return self::$token;
	}

	/**
	 * Resets the stored token. Intended for tests only.
	 *
	 * @param string|null $token Deterministic token to use for subsequent get() calls;
	 *                           omit (or pass null) to force the next get() call to
	 *                           generate a fresh one.
	 *
	 * @return void
	 */
	public static function reset( $token = null ) {
		self::$token = $token;
	}
}
