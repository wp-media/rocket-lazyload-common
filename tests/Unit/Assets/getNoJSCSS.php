<?php
/**
 * Unit tests for the RocketLazyload\Assets::getNoJSCSS method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Assets;

/**
 * Unit test for the RocketLazyload\Assets::getNoJSCSS method
 *
 * @covers RocketLazyload\Assets::getNoJSCSS
 */
class TestGetNoJSCSS extends TestCase
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
     * Test should return CSS in case javascript is disabled
     */
    public function testShouldReturnCSS()
    {
        $this->assertSame(
            '<noscript><style id="rocket-lazyload-nojs-css">.rll-youtube-player, [data-lazy-src]{display:none !important;}</style></noscript>',
            $this->assets->getNoJSCSS()
        );
    }
}
