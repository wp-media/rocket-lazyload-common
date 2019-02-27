<?php
/**
 * Unit tests for the Iframe::lazyloadIframes method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Iframe;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use RocketLazyload\Iframe;

/**
 * Unit tests for the Iframe::lazyloadIframes method
 *
 * @coversDefaultClass RocketLazyload\Iframe
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
        Monkey\setUp();
        $this->iframe = new Iframe();
    }

    /**
     * Do this after each test
     *
     * @return void
     */
    public function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * @covers ::lazyloadIframes
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

        $noiframe = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Iframe/noiframe.html');

        $this->assertSame(
            $noiframe,
            $this->iframe->lazyloadIframes($noiframe, $noiframe)
        );
    }

    /**
     * @covers ::lazyloadIframes
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

        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Iframe/youtube.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Iframe/iframelazyloaded.html');

        $this->assertSame(
            $expected,
            $this->iframe->lazyloadIframes($original, $original)
        );
    }

    /**
     * @covers ::lazyloadIframes
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
        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Iframe/youtube.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/fixtures/Iframe/youtubelazyloaded.html');

        $this->assertSame(
            $expected,
            $this->iframe->lazyloadIframes($original, $original, $args)
        );
    }
}
