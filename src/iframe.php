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
     * @param string $html HTML to parse
     * @param array $args Array of arguments to use
     * @return string
     */
    public function lazyloadIframes($html, $args)
    {
        $defaults = [
            'youtube' => false,
        ];

        $args = wp_parse_args($args, $defaults);

        preg_match_all('/<iframe.*\ssrc=["|\'](.+)["|\'].*(>\s*<\/iframe>)/iU', $html, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return $html;
        }

        foreach ($matches as $iframe) {
            if ($this->isIframeExcluded($iframe)) {
                continue;
            }

            if ($args['youtube'] && false !== strpos($iframe[1], 'youtube')) {
                $youtube_lazyload = $this->replaceYoutubeThumbnail($iframe);

                if (! $youtube_lazyload) {
                    $youtube_lazyload = $this->replaceIframe($iframe);
                }

                $html = str_replace($iframe[0], $youtube_lazyload, $html);
                continue;
            }

            $iframe_lazyload = $this->replaceIframe($iframe);
            $html            = str_replace($iframe[0], $iframe_lazyload, $html);
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
         * @since 2.11
         *
         * @param string $placeholder placeholder that will be printed.
         */
        $placeholder = apply_filters('rocket_lazyload_placeholder', 'about:blank');

        $iframe_noscript = '<noscript>' . $iframe[0] . '</noscript>';

        $iframe_lazyload = str_replace($iframe[1], $placeholder, $iframe[0]);
        $iframe_lazyload = str_replace($iframe[2], ' data-rocket-lazyload="fitvidscompatible" data-lazy-src="' . esc_url($iframe[1]) . '"' . $iframe[2], $iframe_lazyload);

        /**
         * Filter the LazyLoad HTML output on iframes
         *
         * @since 2.11
         *
         * @param array $html Output that will be printed.
         */
        $iframe_lazyload  = apply_filters('rocket_lazyload_iframe_html', $iframe_lazyload);
        $iframe_lazyload .= $iframe_noscript;

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
        $youtube_id = $this->getYoutubeIDFromURL($iframe[1]);

        if (! $youtube_id) {
            return false;
        }

        $query = wp_parse_url(htmlspecialchars_decode($iframe[1]), PHP_URL_QUERY);

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
        $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be|youtube\.com|youtube-nocookie\.com)/(?:embed/|v/|watch/?\?v=)([\w-]{11})#iU';
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
