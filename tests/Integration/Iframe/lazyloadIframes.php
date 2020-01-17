<?php
/**
 * Integration Tests for the RocketLazyload\Iframe::lazyloadIframes method
 *
 * @package RocketLazyload\Tests\Integration
 */

namespace RocketLazyload\Tests\Integration;

use RocketLazyload\Tests\Integration\TestCase;
use RocketLazyload\Iframe;

/**
 * Integration Tests for the RocketLazyload\Iframe::lazyloadIframes
 *
 * @covers RocketLazyload\Iframe::lazyloadIframes
 * @group Iframe
 */
class TestLazyloadIframes extends TestCase
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
     * Test should return the same HTML when no iframe
     */
    public function testShouldReturnSameWhenNoIframe()
    {
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
        $original = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/iframe/youtube.html');
        $expected = file_get_contents(RLL_COMMON_ROOT . 'tests/Fixtures/iframe/iframelazyloaded.html');

        $this->assertSame(
            $expected,
            $this->iframe->lazyloadIframes($original, $original)
        );
    }

    /**
     * Test should return HTML with Youtube iframe lazyloaded
     */
    public function testShouldReturnYoutubeLazyloaded()
    {
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
