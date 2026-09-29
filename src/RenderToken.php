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
 *
 * The token itself is stored in a private static property so every instance
 * shares the same one-per-request value, but the public surface is instance
 * methods, so callers use `( new RenderToken() )->get()` rather than a static
 * call.
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
	public function get() {
		if ( null === self::$token ) {
			self::$token = bin2hex( random_bytes( 8 ) );
		}

		return self::$token;
	}

	/**
	 * Sets a deterministic token. Intended for tests only.
	 *
	 * @param string $token Deterministic token for subsequent get() calls.
	 *
	 * @return void
	 */
	public function set( $token ) {
		self::$token = $token;
	}

	/**
	 * Clears the stored token, so the next get() call generates a fresh one.
	 * Intended for tests only.
	 *
	 * @return void
	 */
	public function reset() {
		self::$token = null;
	}
}
