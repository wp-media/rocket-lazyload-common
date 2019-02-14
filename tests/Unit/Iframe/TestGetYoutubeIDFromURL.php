<?php
/**
 * Unit tests for the Iframe::getYoutubeIDFromURL method
 *
 * @package RocketLazyload
 */

namespace RocketLazyload\Tests\Unit\Iframe;

use PHPUnit\Framework\TestCase;
use RocketLazyload\Iframe;

/**
 * Unit tests for the Iframe::getYoutubeIDFromURL method
 *
 * @coversDefaultClass RocketLazyload\Iframe
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
        $this->iframe = new Iframe();
    }

    /**
     * @covers ::getYoutubeIDfromURL
     * @dataProvider youtubeURLProvider
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
     * @covers ::getYoutubeIDFromURL
     */
    public function testShouldReturnFalseWhenYoutubePlaylist()
    {
        $playlist = 'https://www.youtube.com/embed/videoseries?list=PLx0sYbCqOb8TBPRdmBHs5Iftvv9TPboYG';

        $this->assertFalse(
            $this->iframe->getYoutubeIDFromURL($playlist)
        );
    }

    /**
     * @covers ::getYoutubeIDFromURL
     */
    public function testShouldReturnFalseWhenNotYoutubeURL()
    {
        $vimeo_url = 'https://vimeo.com/279775261';

        $this->assertFalse(
            $this->iframe->getYoutubeIDFromURL($vimeo_url)
        );
    }
}
