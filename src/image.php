<?php
namespace RocketLazyload;

/**
 * A class to provide the methods needed to lazyload images in WP Rocket and Lazyload by WP Rocket
 */
class Image
{
    /**
     * Inserts the lazyload script in the HTML
     *
     * @param array $args Array of arguments to populate the lazyload script options.
     * @return void
     */
    public function insertLazyloadScript($args)
    {
        $defaults = [
            'base_url' => '',
            'suffix'   => 'min',
            'elements' => [
                'img',
                'iframe',
            ],
            'threshold' => 300,
            'version'   => '',
            'fallback'  => '',
        ];

        $args = wp_parse_args($args, $defaults);

        echo '<script>(function(w, d){
            var b = d.getElementsByTagName("body")[0];
            var s = d.createElement("script"); s.async = true;
            s.src = !("IntersectionObserver" in w) ? "' . $args['base_url'] . 'lazyload-' . $args['fallback'] . $args['suffix'] . '.js" : "' . $args['base_url'] . 'lazyload-' . $args['version'] . $args['suffix'] . '.js";
            w.lazyLoadOptions = {
                elements_selector: "' . esc_attr(implode(',', $args['elements'])) . '",
                data_src: "lazy-src",
                data_srcset: "lazy-srcset",
                data_sizes: "lazy-sizes",
                skip_invisible: false,
                class_loading: "lazyloading",
                class_loaded: "lazyloaded",
                threshold: ' . esc_attr($args['threshold']) . ',
                callback_load: function(element) {
                    if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
                        if (element.classList.contains("lazyloaded") ) {
                            if (typeof window.jQuery != "undefined") {
                                if (jQuery.fn.fitVids) {
                                    jQuery(element).parent().fitVids();
                                }
                            }
                        }
                    }
                }
            }; // Your options here. See "recipes" for more information about async.
            b.appendChild(s);
        }(window, document));
        
        // Listen to the Initialized event
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            // Get the instance and puts it in the lazyLoadInstance variable
            var lazyLoadInstance = e.detail.instance;
        
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    lazyLoadInstance.update();
                } );
            } );
            
            var b      = document.getElementsByTagName("body")[0];
            var config = { childList: true, subtree: true };
            
            observer.observe(b, config);
        }, false);
        </script>';
    }

    /**
     * Inserts in the HTML the script to replace the Youtube thumbnail by the iframe.
     *
     * @param array $args Array of arguments to populate the script options.
     * @return void
     */
    public function insertYoutubeThumbnailScript($args)
    {
        $defaults = [
            'resolution' => 'hqdefault',
        ];

        $args = wp_parse_args($args, $defaults);

        echo <<<HTML
        <script>function lazyLoadThumb(e){var t='<img src="https://i.ytimg.com/vi/ID/{$args['resolution']}.jpg">',a='<div class="play"></div>';return t.replace("ID",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement("iframe"),t="https://www.youtube.com/embed/ID?autoplay=1";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute("src",t.replace("ID",this.dataset.id)),e.setAttribute("frameborder","0"),e.setAttribute("allowfullscreen","1"),this.parentNode.replaceChild(e,this)}document.addEventListener("DOMContentLoaded",function(){var e,t,a=document.getElementsByClassName("rll-youtube-player");for(t=0;t<a.length;t++)e=document.createElement("div"),e.setAttribute("data-id",a[t].dataset.id),e.setAttribute("data-query", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>
HTML;
    }

    /**
     * Inserts the CSS to style the Youtube thumbnail container
     *
     * @param array $args Array of arguments to populate the CSS.
     * @return void
     */
    public function insertYoutubeThumbnailCSS($args)
    {
        $defaults = [
            'base_url' => '',
        ];

        $args = wp_parse_args($args, $defaults);

        $css = '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;background:#000;margin:5px}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(' . $args['base_url'] . 'img/youtube.png) no-repeat;cursor:pointer}';

        wp_register_style('rocket-lazyload', false);
        wp_enqueue_style('rocket-lazyload');
        wp_add_inline_style('rocket-lazyload', $css);
    }

    /**
     * Finds the images to be lazyloaded and call the callback method to replace them.
     *
     * @param string $html
     * @return string
     */
    public function lazyloadImages($html)
    {
        return preg_replace_callback('#<img([^>]*) src=("(?:[^"]+)"|\'(?:[^\']+)\'|(?:[^ >]+))([^>]*)>#', [$this,'imageReplaceCallback'], $html);
    }

    /**
     * Modifies the HTML image tag provided to apply lazyload on it.
     *
     * @param array $image Array of matching patterns.
     * @return string
     */
    private function imageReplaceCallback($image)
    {
        // Don't apply LazyLoad on images from WP Retina x2.
        if (function_exists('wr2x_picture_rewrite')) {
            if (wr2x_get_retina(trailingslashit(ABSPATH) . wr2x_get_pathinfo_from_image_src(trim($image[2], '"')))) {
                return $image[0];
            }
        }

        /**
         * Filters the attributes used to prevent lazylad from being applied
         *
         * @since 2.11
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
         * @since 2.11
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
            return $image[0];
        }

        /**
         * Filter the LazyLoad placeholder on src attribute
         *
         * @since 1.1
         *
         * @param string $placeholder Placeholder that will be printed.
         */
        $placeholder = apply_filters('rocket_lazyload_placeholder', 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=');

        $html = sprintf('<img%1$s src="%4$s" data-lazy-src=%2$s%3$s>', $image[1], $image[2], $image[3], $placeholder);

        $html_noscript = sprintf('<noscript><img%1$s src=%2$s%3$s></noscript>', $image[1], $image[2], $image[3]);

        /**
         * Filter the LazyLoad HTML output
         *
         * @since 1.0.2
         *
         * @param array $html Output that will be printed
         */
        $html = apply_filters('rocket_lazyload_html', $html);

        return $html . $html_noscript;
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
     * Applies lazyload on wp_get_attachment_image() function
     *
     * @param array $attr Array of attributes for the image
     * @return array
     */
    public function lazyloadGetAttachmentImage($attr)
    {
        $attr['data-lazy-src'] = $attr['src'];
        // this filter is documented in RocketLazyload\Lazyload.php.
        $attr['src'] = apply_filters('rocket_lazyload_placeholder', 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=');

        if (isset($attr['srcset'])) {
            $attr['data-lazy-srcset'] = $attr['srcset'];
            unset($attr['srcset']);
        }

        if (isset($attr['sizes'])) {
            $attr['data-lazy-sizes'] = $attr['sizes'];
            unset($attr['sizes']);
        }

        return $attr;
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

        // This filter is documented in RocketLazyload\Lazyload.php.
        $placeholder = apply_filters('rocket_lazyload_placeholder', 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=');

        return sprintf(' <img src="%s" data-lazy-src="%s" alt="%s" class="wp-smiley" /> ', $placeholder, esc_url($src_url), esc_attr($smiley));
    }
}
