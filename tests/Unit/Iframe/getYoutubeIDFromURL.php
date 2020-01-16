<?php
/**
 * Unit tests for the RocketLazyload\Iframe::getYoutubeIDFromURL method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Iframe;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Iframe;

/**
 * Unit tests for the RocketLazyload\Iframe::getYoutubeIDFromURL method
 *
 * @covers RocketLazyload\Iframe::getYoutubeIDFromURL
 * @group Iframe
 */
class TestGetYoutubeIDFromURL extends TestCase
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
     * Test should return the ID from the Youtube URL
     *
     * @dataProvider youtubeURLProvider
     *
     * @param string $youtube_url Youtube URL.
     * @param string $expected    Expected ID.
     */
    public function testShouldReturnIDWhenYoutubeURL($youtube_url, $expected)
    {
        $this->assertSame(
            $expected,
            $this->iframe->getYoutubeIDFromURL($youtube_url)
        );
    }

    /**
     * Data provider for testShouldReturnIDWhenYoutubeURL
     *
     * @return array
     */
    public function youtubeURLProvider()
    {
        return [
            ['https://www.youtube.com/watch?v=2h1RfqSOhaw', '2h1RfqSOhaw'],
            ['https://www.youtube-nocookie.com/watch?v=2h1RfqSOhaw', '2h1RfqSOhaw'],
            ['https://youtu.be/2h1RfqSOhaw', '2h1RfqSOhaw'],
            ['https://www.youtube.com/embed/2h1RfqSOhaw', '2h1RfqSOhaw'],
            ['https://www.youtube.com/v/2h1RfqSOhaw', '2h1RfqSOhaw'],
        ];
    }

    /**
     * Test should return false when the Youtube URL is a playlist
     */
    public function testShouldReturnFalseWhenYoutubePlaylist()
    {
        $playlist = 'https://www.youtube.com/embed/videoseries?list=PLx0sYbCqOb8TBPRdmBHs5Iftvv9TPboYG';

        $this->assertFalse(
            $this->iframe->getYoutubeIDFromURL($playlist)
        );
    }

    /**
     * Test should return false when the URL is not a Youtube URL
     */
    public function testShouldReturnFalseWhenNotYoutubeURL()
    {
        $vimeo_url = 'https://vimeo.com/279775261';

        $this->assertFalse(
            $this->iframe->getYoutubeIDFromURL($vimeo_url)
        );
    }
}
