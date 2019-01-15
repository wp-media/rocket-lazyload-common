<?php
namespace RocketLazyload;

/**
 * A class to provide the methods needed to lazyload images in WP Rocket and Lazyload by WP Rocket
 */
class Image
{
    /**
     * Finds the images to be lazyloaded and call the callback method to replace them.
     *
     * @param string $html
     * @param string $buffer
     * @return string
     */
    public function lazyloadImages($html, $buffer)
    {
        preg_match_all('#<img([^>]*) src=("(?:[^"]+)"|\'(?:[^\']+)\'|(?:[^ >]+))([^>]*)>#', $buffer, $images, PREG_SET_ORDER);

        if (empty($images)) {
            return $html;
        }

        foreach ($images as $image) {
            // Don't apply LazyLoad on images from WP Retina x2.
            if (function_exists('wr2x_picture_rewrite')) {
                if (wr2x_get_retina(trailingslashit(ABSPATH) . wr2x_get_pathinfo_from_image_src(trim($image[2], '"')))) {
                    continue;
                }
            }

            /**
             * Filters the attributes used to prevent lazylad from being applied
             *
             * @since 1.0
             * @author Remy Perona
             *
             * @param array $excluded_attributes An array of excluded attributes.
             */
            $excluded_attributes = apply_filters(
                'rocket_lazyload_excluded_attributes',
                [
                    'data-src=',
                    'data-no-lazy=',
                    'data-lazy-original=',
                    'data-lazy-src=',
                    'data-lazysrc=',
                    'data-lazyload=',
                    'data-bgposition=',
                    'data-envira-src=',
                    'fullurl=',
                    'lazy-slider-img=',
                    'data-srcset=',
                    'class="ls-l',
                    'class="ls-bg',
                ]
            );

            /**
             * Filters the src used to prevent lazylad from being applied
             *
             * @since 1.0
             * @author Remy Perona
             *
             * @param array $excluded_src An array of excluded src.
             */
            $excluded_src = apply_filters(
                'rocket_lazyload_excluded_src',
                [
                    '/wpcf7_captcha/',
                    'timthumb.php?src',
                ]
            );

            if ($this->isExcluded($image[1] . $image[3], $excluded_attributes) || $this->isExcluded($image[2], $excluded_src)) {
                continue;
            }

            $image_lazyload = sprintf('<img%1$s src="%4$s" data-lazy-src=%2$s%3$s>', $image[1], $image[2], $image[3], $this->getPlaceholder());

            /**
             * Filter the LazyLoad HTML output
             *
             * @since 1.0
             *
             * @param string $html Output that will be printed
             */
            $image_lazyload = apply_filters('rocket_lazyload_html', $image_lazyload);
            $image_lazyload .= '<noscript>' . $image[0] . '</noscript>';

            $html = str_replace($image[0], $image_lazyload, $html);
        }

        return $html;
    }

    /**
     * Checks if the provided string matches with the provided excluded patterns
     *
     * @param string $string String to check
     * @param array $excluded_values Patterns to match against
     * @return boolean
     */
    private function isExcluded($string, $excluded_values)
    {
        foreach ($excluded_values as $excluded_value) {
            if (strpos($string, $excluded_value) !== false) {
                return true;
            }
        }
    
        return false;
    }

    /**
     * Applies lazyload on srcset and sizes attributes
     *
     * @param string $html HTML image tag
     * @return string
     */
    public function lazyloadResponsiveAttributes($html)
    {
        if (preg_match('/srcset=("(?:[^"]+)"|\'(?:[^\']+)\'|(?:[^ >]+))/i', $html)) {
            $html = str_replace('srcset=', 'data-lazy-srcset=', $html);
        }
    
        if (preg_match('/sizes=("(?:[^"]+)"|\'(?:[^\']+)\'|(?:[^ >]+))/i', $html)) {
            $html = str_replace('sizes=', 'data-lazy-sizes=', $html);
        }
    
        return $html;
    }

    /**
     * Finds patterns matching smiley and call the callback method to replace them with the image
     *
     * @param string $text Content to search in
     * @return string
     */
    public function convertSmilies($text)
    {
        global $wp_smiliessearch;

        if (! get_option('use_smilies') || empty($wp_smiliessearch)) {
            return $text;
        }

        $output = '';
        // HTML loop taken from texturize function, could possible be consolidated.
        $textarr = preg_split('/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between.
        $stop    = count($textarr);// loop stuff.

        // Ignore proessing of specific tags.
        $tags_to_ignore       = 'code|pre|style|script|textarea';
        $ignore_block_element = '';

        for ($i = 0; $i < $stop; $i++) {
            $content = $textarr[ $i ];

            // If we're in an ignore block, wait until we find its closing tag.
            if ('' === $ignore_block_element && preg_match('/^<(' . $tags_to_ignore . ')>/', $content, $matches)) {
                $ignore_block_element = $matches[1];
            }

            // If it's not a tag and not in ignore block.
            if ('' === $ignore_block_element && strlen($content) > 0 && '<' !== $content[0]) {
                $content = preg_replace_callback($wp_smiliessearch, [$this, 'translateSmiley'], $content);
            }

            // did we exit ignore block.
            if ('' !== $ignore_block_element && '</' . $ignore_block_element . '>' === $content) {
                $ignore_block_element = '';
            }

            $output .= $content;
        }

        return $output;
    }

    /**
     * Replace matches by smiley image, lazyloaded
     *
     * @param array $matches Array of matches
     * @return string
     */
    private function translateSmiley($matches)
    {
        global $wpsmiliestrans;

        if (count($matches) === 0) {
            return '';
        }

        $smiley = trim(reset($matches));
        $img    = $wpsmiliestrans[ $smiley ];

        $matches    = [];
        $ext        = preg_match('/\.([^.]+)$/', $img, $matches) ? strtolower($matches[1]) : false;
        $image_exts = ['jpg', 'jpeg', 'jpe', 'gif', 'png'];

        // Don't convert smilies that aren't images - they're probably emoji.
        if (! in_array($ext, $image_exts, true)) {
            return $img;
        }

        /**
         * Filter the Smiley image URL before it's used in the image element.
         *
         * @since 2.9.0
         *
         * @param string $smiley_url URL for the smiley image.
         * @param string $img        Filename for the smiley image.
         * @param string $site_url   Site URL, as returned by site_url().
         */
        $src_url = apply_filters('smilies_src', includes_url("images/smilies/$img"), $img, site_url());

        // Don't LazyLoad if process is stopped for these reasons.
        if (is_feed() || is_preview()) {
            return sprintf(' <img src="%s" alt="%s" class="wp-smiley" /> ', esc_url($src_url), esc_attr($smiley));
        }

        return sprintf(' <img src="%s" data-lazy-src="%s" alt="%s" class="wp-smiley" /> ', $this->getPlaceholder(), esc_url($src_url), esc_attr($smiley));
    }

    /**
     * Returns the placeholder for the src attribute
     *
     * @since 1.2
     * @author Remy Perona
     *
     * @param int $width  Width of the placeholder image. Default 1.
     * @param int $height Height of the placeholder image. Default 1.
     * @return void
     */
    private function getPlaceholder($width = 1, $height = 1)
    {
        /**
         * Filter the image lazyLoad placeholder on src attribute
         *
         * @since 1.1
         *
         * @param string $placeholder Placeholder that will be printed.
         */
        return apply_filters('rocket_lazyload_placeholder', "data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 $width $height\'%3E%3C/svg%3E");
    }
}
