<?php

namespace RocketLazyload\Tests\Unit\Iframe;

use RocketLazyload\Tests\Unit\TestCase;
use RocketLazyload\Iframe;

/**
 * @covers RocketLazyload\Iframe::isIframeExcluded
 * @group  Iframe
 */
class Test_IsIframeExcluded extends TestCase {
	private $iframe;

	protected function set_up() {
		parent::set_up();
		$this->iframe = new Iframe();
	}

	/**
	 * @dataProvider iframeExcludedPatternProvider
	 *
	 * @param array $iframe Array containing the iframe HTML and iframe attributes.
	 */
	public function testShouldReturnTrueWhenExcludedPattern( $iframe ) {
		$this->assertTrue(
			$this->iframe->isIframeExcluded( $iframe )
		);
	}

	/**
	 * Data Provider for testShouldReturnTrueWhenExcludedPattern.
	 *
	 * @return array
	 */
	public function iframeExcludedPatternProvider() {
		return [
			[
				[
					'<iframe id="gform_ajax_frame_1" name="gform_ajax_frame_1" src="about:blank" style="display:none;width:0px; height:0px;"></iframe>',
					'id="gform_ajax_frame_1" name="gform_ajax_frame_1" src="about:blank" style="display:none;width:0px; height:0px;"',
					'id="gform_ajax_frame_1" name="gform_ajax_frame_1" src="about:blank" style="display:none;width:0px; height:0px;"',
				],
			],
			[
				[
					'<iframe src="https://www.google.com/recaptcha/api/fallback?k=XXX&amp;hl=en&amp;v=r20150812220525&amp;t=1&amp;ff=true" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>',
					'src="https://www.google.com/recaptcha/api/fallback?k=XXX&amp;hl=en&amp;v=r20150812220525&amp;t=1&amp;ff=true" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"',
					'src="https://www.google.com/recaptcha/api/fallback?k=XXX&amp;hl=en&amp;v=r20150812220525&amp;t=1&amp;ff=true" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"',
				],
			],
			[
				[
					'<iframe width="560" height="315" data-no-lazy="1" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
					'width="560" height="315" data-no-lazy="1" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen',
					'width="560" height="315" data-no-lazy="1" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen',
				],
			],
			[
				[
					'<iframe width="560" height="315" data-skip-lazy="1" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
					'width="560" height="315" data-skip-lazy="1" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen',
					'width="560" height="315" data-skip-lazy="1" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen',
				],
			],
			[
				[
					'<iframe width="560" height="315" class="skip-lazy" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
					'width="560" height="315" class="skip-lazy" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen',
					'width="560" height="315" class="skip-lazy" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen',
				],
			],
		];
	}

	/**
	 * @dataProvider iframeProvider
	 *
	 * @param array $iframe Array containing the iframe HTML and iframe attributes.
	 */
	public function testShouldReturnFalseWhenNotExcludedPattern( $iframe ) {
		$this->assertFalse(
			$this->iframe->isIframeExcluded( $iframe )
		);
	}

	/**
	 * Data provider for testShouldReturnFalseWhenNotExcludedPattern.
	 *
	 * @return array
	 */
	public function iframeProvider() {
		return [
			[
				[
					'<iframe width="560" height="315" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
					'width="560" height="315" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen',
					'width="560" height="315" src="https://www.youtube.com/embed/Y7pVUaPJeg8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen',
				],
			],
		];
	}
}
