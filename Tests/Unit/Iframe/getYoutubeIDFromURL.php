<?php

namespace RocketLazyload\Tests\Unit\Iframe;

use RocketLazyload\Iframe;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * @covers RocketLazyload\Iframe::getYoutubeIDFromURL
 * @group  Iframe
 */
class Test_GetYoutubeIDFromURL extends TestCase {
	private $iframe;

	public function setUp() {
		parent::setUp();
		$this->iframe = new Iframe();
	}

	/**
	 * @dataProvider youtubeURLProvider
	 *
	 * @param string $youtube_url Youtube URL.
	 * @param string $expected    Expected ID.
	 */
	public function testShouldReturnIDWhenYoutubeURL( $youtube_url, $expected ) {
		$this->assertSame(
			$expected,
			$this->iframe->getYoutubeIDFromURL( $youtube_url )
		);
	}

	/**
	 * Data provider for testShouldReturnIDWhenYoutubeURL.
	 *
	 * @return array
	 */
	public function youtubeURLProvider() {
		return [
			[ 'https://www.youtube.com/watch?v=2h1RfqSOhaw', '2h1RfqSOhaw' ],
			[ 'https://www.youtube-nocookie.com/watch?v=2h1RfqSOhaw', '2h1RfqSOhaw' ],
			[ 'https://youtu.be/2h1RfqSOhaw', '2h1RfqSOhaw' ],
			[ 'https://www.youtube.com/embed/2h1RfqSOhaw', '2h1RfqSOhaw' ],
			[ 'https://www.youtube.com/v/2h1RfqSOhaw', '2h1RfqSOhaw' ],
		];
	}

	public function testShouldReturnFalseWhenYoutubePlaylist() {
		$playlist = 'https://www.youtube.com/embed/videoseries?list=PLx0sYbCqOb8TBPRdmBHs5Iftvv9TPboYG';

		$this->assertFalse(
			$this->iframe->getYoutubeIDFromURL( $playlist )
		);
	}

	public function testShouldReturnFalseWhenNotYoutubeURL() {
		$vimeo_url = 'https://vimeo.com/279775261';

		$this->assertFalse(
			$this->iframe->getYoutubeIDFromURL( $vimeo_url )
		);
	}
}
