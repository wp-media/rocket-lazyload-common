<?php
namespace RocketLazyload;

/**
 * A class to provide the methods needed to lazyload iframes in WP Rocket and Lazyload by WP Rocket
 */
class Iframe
{
    /**
     * Finds iframes in the HTML provided and call the methods to lazyload them
     *
     * @param string $html Original HTML
     * @param string $buffer Content to parse
     * @param array $args Array of arguments to use
     * @return string
     */
    public function lazyloadIframes($html, $buffer, $args)
    {
        $defaults = [
            'youtube' => false,
        ];

        $args = wp_parse_args($args, $defaults);

        preg_match_all('@<iframe(?<atts>\s.+)>.*</iframe>@iUs', $buffer, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return $html;
        }

        foreach ($matches as $iframe) {
            if ($this->isIframeExcluded($iframe)) {
                continue;
            }

            // Given the previous regex pattern, $iframe['atts'] starts with a whitespace character.
            if (! preg_match('@\ssrc\s*=\s*(\'|")(?<src>.*)\1@iUs', $iframe['atts'], $atts)) {
                continue;
            }

            $iframe['src'] = trim($atts['src']);

            if ('' === $iframe['src']) {
                continue;
            }

            if ($args['youtube']) {
                $iframe_lazyload = $this->replaceYoutubeThumbnail($iframe);
            }

            if (empty($iframe_lazyload)) {
                $iframe_lazyload = $this->replaceIframe($iframe);
            }

            $html = str_replace($iframe[0], $iframe_lazyload, $html);

            unset($iframe_lazyload);
        }

        return $html;
    }

    /**
     * Checks if the provided iframe is excluded from lazyload
     *
     * @param array $iframe Array of matched patterns
     * @return boolean
     */
    private function isIframeExcluded($iframe)
    {
        // Don't mess with the Gravity Forms ajax iframe.
        if (strpos($iframe[0], 'gform_ajax_frame')) {
            return true;
        }

        // Don't lazyload if iframe has data-no-lazy attribute.
        if (strpos($iframe[0], 'data-no-lazy=')) {
            return true;
        }

        // Don't lazyload if iframe is google recaptcha fallback.
        if (strpos($iframe[0], 'recaptcha/api/fallback')) {
            return true;
        }

        return false;
    }

    /**
     * Applies lazyload on the iframe provided
     *
     * @param array $iframe Array of matched elements
     * @return string
     */
    private function replaceIframe($iframe)
    {
        /**
         * Filter the LazyLoad placeholder on src attribute
         *
         * @since 1.0
         *
         * @param string $placeholder placeholder that will be printed.
         */
        $placeholder = apply_filters('rocket_lazyload_placeholder', 'about:blank');

        $placeholder_atts = str_replace($iframe['src'], $placeholder, $iframe['atts']);
        $iframe_lazyload  = str_replace($iframe['atts'], $placeholder_atts . ' data-rocket-lazyload="fitvidscompatible" data-lazy-src="' . esc_url($iframe['src']) . '"', $iframe[0]);

        /**
         * Filter the LazyLoad HTML output on iframes
         *
         * @since 1.0
         *
         * @param array $html Output that will be printed.
         */
        $iframe_lazyload  = apply_filters('rocket_lazyload_iframe_html', $iframe_lazyload);
        $iframe_lazyload .= '<noscript>' . $iframe[0] . '</noscript>';

        return $iframe_lazyload;
    }

    /**
     * Replaces the iframe provided by the Youtube thumbnail
     *
     * @param array $iframe Array of matched elements
     * @return bool|string
     */
    private function replaceYoutubeThumbnail($iframe)
    {
        $youtube_id = $this->getYoutubeIDFromURL($iframe['src']);

        if (! $youtube_id) {
            return false;
        }

        $query = wp_parse_url(htmlspecialchars_decode($iframe['src']), PHP_URL_QUERY);

        /**
         * Filter the LazyLoad HTML output on Youtube iframes
         *
         * @since 2.11
         *
         * @param array $html Output that will be printed.
         */
        $youtube_lazyload  = apply_filters('rocket_lazyload_youtube_html', '<div class="rll-youtube-player" data-id="' . esc_attr($youtube_id) . '" data-query="' . esc_attr($query) . '"></div>');
        $youtube_lazyload .= '<noscript>' . $iframe[0] . '</noscript>';

        return $youtube_lazyload;
    }

    /**
     * Gets the Youtube ID from the URL provided
     *
     * @param string $url URL to search
     * @return bool|string
     */
    private function getYoutubeIDFromURL($url)
    {
        $pattern = '#^(?:https?:)?(?://)?(?:www\.)?(?:youtu\.be|youtube\.com|youtube-nocookie\.com)/(?:embed/|v/|watch/?\?v=)([\w-]{11})#iU';
        $result  = preg_match($pattern, $url, $matches);

        if (! $result) {
            return false;
        }

        // exclude playlist.
        if ('videoseries' === $matches[1]) {
            return false;
        }

        return $matches[1];
    }
}
