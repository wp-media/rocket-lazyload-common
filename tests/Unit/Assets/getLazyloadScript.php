<?php
/**
 * Unit tests for the RocketLazyload\Assets::getLazyloadScript method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Assets;
use Brain\Monkey\Functions;

/**
 * Unit test for the RocketLazyload\Assets::getLazyloadScript method
 *
 * @covers RocketLazyload\Assets::getLazyloadScript
 */
class TestGetLazyloadScript extends TestCase
{
    /**
     * Assets instance
     *
     * @var Assets
     */
    private $assets;

    /**
     * Do this before each test
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->assets = new Assets();
    }
    
    /**
     * Test should return lazyload script HTML without minified extension
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testShouldReturnLazyloadScriptWhenScriptDebug()
    {
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

        define('SCRIPT_DEBUG', true);

        $args = [
            'base_url' => 'http://example.org/',
            'version'  => '11.0.2',
        ];

        $expected = '<script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.js"></script>';

        $this->assertSame(
            $expected,
            $this->assets->getLazyloadScript($args)
        );
    }

    /**
     * Test should return lazyload script HTML with minified extension
     */
    public function testShouldReturnMinLazyloadScriptWhenNoScriptDebug()
    {
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

        $args = [
            'base_url' => 'http://example.org/',
            'version'  => '11.0.2',
        ];

        $expected = '<script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.min.js"></script>';

        $this->assertSame(
            $expected,
            $this->assets->getLazyloadScript($args)
        );
    }

    /**
     * Test should return lazyload script HTML with the polyfill for intersection observer
     */
    public function testShouldReturnLazyloadScriptWithPolyfill()
    {
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

        $args = [
            'base_url' => 'http://example.org/',
            'version'  => '11.0.2',
            'polyfill' => true,
        ];

        $expected = '<script crossorigin="anonymous" src="https://polyfill.io/v3/polyfill.min.js?flags=gated&features=default%2CIntersectionObserver%2CIntersectionObserverEntry"></script><script data-no-minify="1" async src="http://example.org/11.0.2/lazyload.min.js"></script>';

        $this->assertSame(
            $expected,
            $this->assets->getLazyloadScript($args)
        );
    }
}
