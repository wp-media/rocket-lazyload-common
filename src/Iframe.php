<?php
/**
 * Handles lazyloading of iframes
 *
 * @package RocketLazyload
 */

namespace RocketLazyload;

/**
 * A class to provide the methods needed to lazyload iframes in WP Rocket and Lazyload by WP Rocket
 */
class Iframe {

	/**
	 * Finds iframes in the HTML provided and call the methods to lazyload them
	 *
	 * @param string      $html   Original HTML.
	 * @param string      $buffer Content to parse.
	 * @param array<bool> $args   Array of arguments to use.
	 *
	 * @return string
	 */
	public function lazyloadIframes( $html, $buffer, $args = [] ) {
		$defaults = [
			'youtube' => false,
		];

		$args = wp_parse_args( $args, $defaults );

		if ( ! preg_match_all( '@<iframe(?<atts>\s.+)>.*</iframe>@iUs', $buffer, $iframes, PREG_SET_ORDER ) ) {
			return $html;
		}

		$iframes = array_unique( $iframes, SORT_REGULAR );

		foreach ( $iframes as $iframe ) {
			if ( $this->isIframeExcluded( $iframe ) ) {
				continue;
			}

			// Given the previous regex pattern, $iframe['atts'] starts with a whitespace character.
			if ( ! preg_match( '@\ssrc\s*=\s*(\'|")(?<src>.*)\1@iUs', $iframe['atts'], $atts ) ) {
				continue;
			}

			$iframe['src'] = trim( $atts['src'] );

			if ( '' === $iframe['src'] ) {
				continue;
			}

			$iframe_lazyload = '';

			if ( $args['youtube'] ) {
				$iframe_lazyload = $this->replaceYoutubeThumbnail( $iframe );
			}

			if ( empty( $iframe_lazyload ) ) {
				$iframe_lazyload = $this->replaceIframe( $iframe );
			}

			$html = str_replace( $iframe[0], $iframe_lazyload, $html );

			unset( $iframe_lazyload );
		}

		return $html;
	}

	/**
	 * Checks if the provided iframe is excluded from lazyload
	 *
	 * @param array<string> $iframe Array of matched patterns.
	 * @return boolean
	 */
	public function isIframeExcluded( $iframe ) {

		foreach ( $this->getExcludedPatterns() as $excluded_pattern ) {
			if ( strpos( $iframe[0], $excluded_pattern ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets patterns excluded from lazyload for iframes
	 *
	 * @since 2.1.1
	 *
	 * @return array<string>
	 */
	private function getExcludedPatterns() {
		/**
		 * Filters the patterns excluded from lazyload for iframes
		 *
		 * @since 2.1.1
		 *
		 * @param array $excluded_patterns Array of excluded patterns.
		 */
		return apply_filters(
			'rocket_lazyload_iframe_excluded_patterns',
			[
				'gform_ajax_frame',
				'data-no-lazy=',
				'recaptcha/api/fallback',
				'loading="eager"',
				'data-skip-lazy',
				'skip-lazy',
				'google_ads_iframe_',
			]
		);
	}

	/**
	 * Applies lazyload on the iframe provided
	 *
	 * @param array<string> $iframe Array of matched elements.
	 *
	 * @return string
	 */
	private function replaceIframe( $iframe ) {
		/**
		 * Filter the LazyLoad placeholder on src attribute
		 *
		 * @since 1.0
		 *
		 * @param string $placeholder placeholder that will be printed.
		 */
		$placeholder = apply_filters( 'rocket_lazyload_placeholder', 'about:blank' );

		$placeholder_atts = str_replace( $iframe['src'], $placeholder, $iframe['atts'] );
		$iframe_lazyload  = str_replace( $iframe['atts'], $placeholder_atts . ' data-rocket-lazyload="fitvidscompatible" data-lazy-src="' . esc_url( $iframe['src'] ) . '"', $iframe[0] );

		if ( ! preg_match( '@\sloading\s*=\s*(\'|")(?:lazy|auto)\1@i', $iframe_lazyload ) ) {
			$iframe_lazyload = str_replace( '<iframe', '<iframe loading="lazy"', $iframe_lazyload );
		}

		/**
		 * Filter the LazyLoad HTML output on iframes
		 *
		 * @since 1.0
		 *
		 * @param string $html Output that will be printed.
		 */
		$iframe_lazyload  = apply_filters( 'rocket_lazyload_iframe_html', $iframe_lazyload );
		$iframe_lazyload .= '<noscript>' . $iframe[0] . '</noscript>';

		return $iframe_lazyload;
	}

	/**
	 * Replaces the iframe provided by the Youtube thumbnail
	 *
	 * @param array<string> $iframe Array of matched elements.
	 *
	 * @return string
	 */
	private function replaceYoutubeThumbnail( $iframe ) {
		$youtube_id = $this->getYoutubeIDFromURL( $iframe['src'] );

		if ( '' === $youtube_id ) {
			return '';
		}

		$query = wp_parse_url( htmlspecialchars_decode( $iframe['src'] ), PHP_URL_QUERY );

		if ( ! is_string( $query ) ) {
			$query = '';
		}

		$youtube_url = $this->changeYoutubeUrlForYoutuDotBe( $iframe['src'] );
		$youtube_url = $this->cleanYoutubeUrl( $iframe['src'] );

		preg_match( '@\s*title\s*=\s*(\'|")(?<title>.*)\1@iUs', $iframe['atts'], $atts );

		$title = $atts['title'] ?? '';
		/**
		 * Filter the LazyLoad HTML output on Youtube iframes
		 *
		 * @since 2.11
		 *
		 * @param string $html Output that will be printed.
		 */
		$youtube_lazyload  = apply_filters( 'rocket_lazyload_youtube_html', '<div class="rll-youtube-player" data-src="' . esc_attr( $youtube_url ) . '" data-id="' . esc_attr( $youtube_id ) . '" data-query="' . esc_attr( $query ) . '" data-alt="' . esc_attr( $title ) . '"></div>' );
		$youtube_lazyload  = $this->addRenderToken( $youtube_lazyload );
		$youtube_lazyload .= '<noscript>' . $iframe[0] . '</noscript>';

		return $youtube_lazyload;
	}

	/**
	 * Marks every placeholder in the (already filtered) markup with the current
	 * request's render token, so the companion inline script can recognise it
	 * as something this library rendered.
	 *
	 * Applied after the `rocket_lazyload_youtube_html` filter runs, and scans
	 * every `<div>` opening tag in the result for a genuine `class` attribute
	 * containing the `rll-youtube-player` token -- quoted with either `"` or
	 * `'`, unquoted, in any attribute position, and never a `class=`-looking
	 * substring nested inside another attribute's value. This way a filter
	 * that only reorders/adds attributes, changes the quote style, or emits
	 * more than one placeholder keeps working exactly as before. A filter that
	 * removes that class from an element already opts it out of the feature;
	 * nothing further is needed for that case.
	 *
	 * @param string $html Youtube placeholder markup, already filtered.
	 *
	 * @return string
	 */
	private function addRenderToken( $html ) {
		$result = preg_replace_callback(
			'#<div\b(?:[^>"\']++|"[^"]*+"|\'[^\']*+\')*+>#i',
			[ $this, 'addRenderTokenToDivTag' ],
			$html
		);

		return null === $result ? $html : $result;
	}

	/**
	 * Adds the render token to a single `<div>` tag, if it is a genuine
	 * `rll-youtube-player` placeholder that does not already carry one.
	 *
	 * A tag that already has a `data-rll-token` attribute (e.g. one
	 * hand-authored directly in content) is left alone rather than overwritten:
	 * the inline script only ever compares it against the current request's
	 * token, so a stale or content-supplied value simply never matches and
	 * that element is skipped, exactly as if it had no token at all.
	 *
	 * @param array<int, string> $matches Regex match set; [0] is the full tag.
	 *
	 * @return string
	 */
	private function addRenderTokenToDivTag( $matches ) {
		$tag   = $matches[0];
		$class = $this->findAttribute( $tag, 'class' );

		if ( ! $class || ! $this->hasClassToken( $class['value'], 'rll-youtube-player' ) ) {
			return $tag;
		}

		if ( $this->findAttribute( $tag, 'data-rll-token' ) ) {
			return $tag;
		}

		return substr( $tag, 0, 4 ) . ' data-rll-token="' . esc_attr( ( new RenderToken() )->get() ) . '"' . substr( $tag, 4 );
	}

	/**
	 * Checks whether a (still-quoted, if applicable) class attribute value
	 * contains the given whitespace-separated class token, case-sensitively,
	 * the same way a browser matches CSS classes.
	 *
	 * @param string $raw_value Attribute value as returned by findAttribute(), still quoted if applicable.
	 * @param string $token     Class token to look for, e.g. `rll-youtube-player`.
	 *
	 * @return bool
	 */
	private function hasClassToken( $raw_value, $token ) {
		$value  = $this->stripOuterQuoteChars( $raw_value );
		$tokens = preg_split( '/\s+/', trim( $value ) );

		return is_array( $tokens ) && in_array( $token, $tokens, true );
	}

	/**
	 * Removes a matching pair of leading/trailing quote characters from an
	 * attribute value, without trimming any whitespace. Small, Iframe-local
	 * equivalent of Image::stripOuterQuoteChars().
	 *
	 * @param string $value Attribute value, as returned by findAttribute().
	 *
	 * @return string
	 */
	private function stripOuterQuoteChars( $value ) {
		$length = strlen( $value );

		if ( $length < 2 ) {
			return $value;
		}

		$first = $value[0];
		$last  = $value[ $length - 1 ];

		if ( ( '"' === $first || "'" === $first ) && $first === $last ) {
			return substr( $value, 1, -1 );
		}

		return $value;
	}

	/**
	 * Finds the first genuine, non-nested occurrence of the given attribute on
	 * an HTML tag string. Small, Iframe-local equivalent of
	 * Image::findRealAttribute()'s quote-aware, linear (single-pass) lookup,
	 * kept separate since that one is private to Image.
	 *
	 * @param string $tag  HTML tag string to search in, e.g. `<div class="a">`.
	 * @param string $name Attribute name to look for, e.g. `class`.
	 *
	 * @return false|array{attribute: string, value: string}
	 */
	private function findAttribute( $tag, $name ) {
		$pattern = '#(?<=\s)' . preg_quote( $name, '#' ) . '\s*=\s*(?<value>"[^"]*+"|\'[^\']*+\'|[^\s>]++)#i';

		if ( ! preg_match_all( $pattern, $tag, $matches, PREG_OFFSET_CAPTURE ) ) {
			return false;
		}

		$pos        = 0;
		$open_quote = null;

		foreach ( $matches[0] as $index => $match ) {
			$offset = $match[1];

			for ( $i = $pos; $i < $offset; $i++ ) {
				$char = $tag[ $i ];

				if ( null === $open_quote ) {
					if ( '"' === $char || "'" === $char ) {
						$open_quote = $char;
					}

					continue;
				}

				if ( $char === $open_quote ) {
					$open_quote = null;
				}
			}

			$pos = $offset;

			if ( null !== $open_quote ) {
				continue;
			}

			return [
				'attribute' => $match[0],
				'value'     => $matches['value'][ $index ][0],
			];
		}

		return false;
	}

	/**
	 * Gets the Youtube ID from the URL provided
	 *
	 * @param string $url URL to search.
	 *
	 * @return string
	 */
	public function getYoutubeIDFromURL( $url ) {
		$pattern = '#^(?:https?:)?(?://)?(?:www\.)?(?:youtu\.be|youtube\.com|youtube-nocookie\.com)/(?:embed/|v/|watch/?\?v=)?([\w-]{11})#iU';
		$result  = preg_match( $pattern, $url, $matches );

		if ( ! $result ) {
			return '';
		}

		// exclude playlist.
		if ( 'videoseries' === $matches[1] ) {
			return '';
		}

		return $matches[1];
	}

	/**
	 * Changes URL youtu.be/ID to youtube.com/embed/ID
	 *
	 * @param  string $url URL to replace.
	 * @return string      Unchanged URL or modified URL.
	 */
	public function changeYoutubeUrlForYoutuDotBe( $url ) {
		$pattern = '#^(?:https?:)?(?://)?(?:www\.)?(?:youtu\.be)/(?:embed/|v/|watch/?\?v=)?([\w-]{11})#iU';
		$result  = preg_match( $pattern, $url, $matches );

		if ( ! $result ) {
			return $url;
		}

		return 'https://www.youtube.com/embed/' . $matches[1];
	}

	/**
	 * Cleans Youtube URL. Keeps only scheme, host and path.
	 *
	 * @param  string $url URL to be cleaned.
	 * @return string      Cleaned URL
	 */
	public function cleanYoutubeUrl( $url ) {
		$parsed_url = wp_parse_url( $url, -1 );
		$scheme     = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '//';
		$host       = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
		$path       = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';

		return $scheme . $host . $path;
	}
}
