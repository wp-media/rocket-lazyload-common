<?php
/**
 * Unit tests for the RocketLazyload\Iframe::lazyloadIframes method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Iframe;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Iframe;
use Brain\Monkey\Functions;

/**
 * Unit tests for the RocketLazyload\Iframe::lazyloadIframes method
 *
 * @covers RocketLazyload\Iframe::lazyloadIframes
 * @group Iframe
 */
class TestLazyloadIframe extends TestCase
{
    /**
     * Iframe instance
     *
     * @var Iframe
     */
    private $iframe;

    /**
     * Do this before each test
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->iframe = new Iframe();
    }

    /**
     * Test should return same HTML when no iframe
     */
    public function testShouldReturnSameWhenNoIframe()
    {
        Functions\when('esc_url')->returnArg();
        Functions\when('wp_parse_args')->alias(function ($args, $defaults) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                parse_str($args, $r);
            }
        
            if (is_array($defaults)) {
                return array_merge($defaults, $r);
            }

            return $r;
        });

        $noiframe = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/iframe/noiframe.html');

        $this->assertSame(
            $noiframe,
            $this->iframe->lazyloadIframes($noiframe, $noiframe)
        );
    }

    /**
     * Test should return HTML with iframes lazyloaded
     */
    public function testShouldReturnIframeLazyloaded()
    {
        Functions\when('esc_url')->returnArg();
        Functions\when('wp_parse_args')->alias(function ($args, $defaults) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                parse_str($args, $r);
            }
        
            if (is_array($defaults)) {
                return array_merge($defaults, $r);
            }

            return $r;
        });

        $original = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/iframe/youtube.html');
        $expected = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/iframe/iframelazyloaded.html');

        $this->assertSame(
            $expected,
            $this->iframe->lazyloadIframes($original, $original)
        );
    }

    /**
     * Test should return HTML with youtube iframes lazyloaded
     */
    public function testShouldReturnYoutubeLazyloaded()
    {
        Functions\when('esc_attr')->returnArg();
        Functions\when('wp_parse_args')->alias(function ($args, $defaults) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                parse_str($args, $r);
            }
        
            if (is_array($defaults)) {
                return array_merge($defaults, $r);
            }

            return $r;
        });
        Functions\when('wp_parse_url')->alias(function ($url, $component) {
            return parse_url($url, $component);
        });

        $args     = [
            'youtube' => true,
        ];
        $original = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/iframe/youtube.html');
        $expected = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/iframe/youtubelazyloaded.html');

        $this->assertSame(
            $expected,
            $this->iframe->lazyloadIframes($original, $original, $args)
        );
    }
}
