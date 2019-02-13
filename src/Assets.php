<?php
/**
 * Handle the lazyload required assets: inline CSS and JS
 *
 * @package RocketLazyload
 */

namespace RocketLazyload;

/**
 * Class containing the methods to return or print the assets needed for lazyloading
 */
class Assets
{
    /**
     * Inserts the lazyload script in the HTML
     *
     * @param array $args Array of arguments to populate the lazyload script options.
     * @return void
     */
    public function insertLazyloadScript($args = [])
    {
        echo $this->getLazyloadScript($args);
    }

    /**
     * Returns the lazyload inline script
     *
     * @param array $args Array of arguments to populate the lazyload script options.
     * @return string
     */
    public function getLazyloadScript($args = [])
    {
        $defaults = [
            'base_url'  => '',
            'elements'  => [
                'img',
                'iframe',
            ],
            'threshold' => 300,
            'version'   => '',
            'fallback'  => '',
        ];

        $args = wp_parse_args($args, $defaults);
        $min  = defined('SCRIPT_DEBUG') ? '' : '.min';

        return '<script>(function(w, d){
            var b = d.getElementsByTagName("body")[0];
            var s = d.createElement("script"); s.async = true;
            s.src = !("IntersectionObserver" in w) ? "' . $args['base_url'] . 'lazyload-' . $args['fallback'] . $min . '.js" : "' . $args['base_url'] . 'lazyload-' . $args['version'] . $min . '.js";
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
        
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        lazyLoadInstance.update();
                    } );
                } );
                
                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };
                
                observer.observe(b, config);
            }
        }, false);
        </script>';
    }

    /**
     * Inserts in the HTML the script to replace the Youtube thumbnail by the iframe.
     *
     * @param array $args Array of arguments to populate the script options.
     * @return void
     */
    public function insertYoutubeThumbnailScript($args = [])
    {
        echo $this->getYoutubeThumbnailScript($args);
    }

    /**
     * Returns the Youtube Thumbnail inline script
     *
     * @param array $args Array of arguments to populate the script options.
     * @return string
     */
    public function getYoutubeThumbnailScript($args = [])
    {
        $defaults = [
            'resolution' => 'hqdefault',
        ];

        $allowed_resolutions = [
            'default'       => 1,
            'mqdefault'     => 1,
            'sddefault'     => 1,
            'hqdefault'     => 1,
            'maxresdefault' => 1,
        ];

        $args['resolution'] = ( isset($args['resolution']) && isset($allowed_resolutions[ $args['resolution'] ]) ) ? $args['resolution'] : 'hqdefault';

        $args = wp_parse_args($args, $defaults);

        return "<script>function lazyLoadThumb(e){var t='<img src=\"https://i.ytimg.com/vi/ID/{$args['resolution']}.jpg\">',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>";
    }

    /**
     * Inserts the CSS to style the Youtube thumbnail container
     *
     * @param array $args Array of arguments to populate the CSS.
     * @return void
     */
    public function insertYoutubeThumbnailCSS($args = [])
    {
        wp_register_style('rocket-lazyload', false);
        wp_enqueue_style('rocket-lazyload');
        wp_add_inline_style('rocket-lazyload', $this->getYoutubeThumbnailCSS($args));
    }

    /**
     * Returns the CSS for the Youtube Thumbnail
     *
     * @param array $args Array of arguments to populate the CSS.
     * @return string
     */
    public function getYoutubeThumbnailCSS($args = [])
    {
        $defaults = [
            'base_url' => '',
        ];

        $args = wp_parse_args($args, $defaults);

        return '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(' . $args['base_url'] . 'img/youtube.png) no-repeat;cursor:pointer}.wp-has-aspect-ratio .rll-youtube-player{position:absolute;padding-bottom:0;width:100%;height:100%;top:0;bottom:0;left:0;right:0;';
    }

    /**
     * Inserts the CSS needed when Javascript is not enabled to keep the display correct
     */
    public function insertNoJSCSS()
    {
        wp_register_style('rocket-lazyload', false);
        wp_enqueue_style('rocket-lazyload');
        wp_add_inline_style('rocket-lazyload', $this->getNoJSCSS());
    }

    /**
     * Returns the CSS to correctly display images when JavaScript is disabled
     *
     * @return string
     */
    public function getNoJSCSS()
    {
        return '.no-js .rll-youtube-player, .no-js [data-lazy-src]{display:none !important;}';
    }
}
