<?php
/**
 * Integration Tests for the Iframe class
 *
 * @package RocketLazyload
 */

namespace Rocketlazyload\Tests\Integration;

use WP_UnitTestCase;
use RocketLazyload\Iframe;

/**
 * Integration Tests for the Iframe class
 */
class TestIframe extends WP_UnitTestCase
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
        $this->iframe = new Iframe();
    }

    /**
     * @covers ::lazyloadIframes
     */
    public function testShouldReturnSameWhenNoIframe()
    {
        $noiframe = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Iframe/noiframe.html');

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
        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Iframe/youtube.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Iframe/iframelazyloaded.html');

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
        $args     = [
            'youtube' => true,
        ];
        $original = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Iframe/youtube.html');
        $expected = \file_get_contents( RLL_COMMON_TESTS_ROOT . '/contentProvider/Iframe/youtubelazyloaded.html');

        $this->assertSame(
            $expected,
            $this->iframe->lazyloadIframes($original, $original, $args)
        );
    }
}
