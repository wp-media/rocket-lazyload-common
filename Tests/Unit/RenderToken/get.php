<?php

namespace RocketLazyload\Tests\Unit\RenderToken;

use RocketLazyload\RenderToken;
use RocketLazyload\Tests\Unit\TestCase;

/**
 * Tests for the RocketLazyload\RenderToken::get method
 *
 * @covers RocketLazyload\RenderToken::get
 * @uses RocketLazyload\RenderToken::reset
 * @group  RenderToken
 */
class Test_Get extends TestCase {
	protected function tear_down() {
		RenderToken::reset();
		parent::tear_down();
	}

	public function testShouldGenerateAHexTokenOfAtLeast16Characters() {
		RenderToken::reset();

		$token = RenderToken::get();

		$this->assertMatchesRegularExpression( '/^[0-9a-f]{16,}$/', $token );
	}

	public function testShouldReturnTheSameTokenOnEveryCallWithinOneRequest() {
		RenderToken::reset();

		$first  = RenderToken::get();
		$second = RenderToken::get();

		$this->assertSame( $first, $second );
	}

	public function testShouldReturnADifferentTokenAfterANewRequestResetsIt() {
		RenderToken::reset( 'aaaaaaaaaaaaaaaa' );
		$before = RenderToken::get();

		RenderToken::reset();
		$after = RenderToken::get();

		$this->assertNotSame( $before, $after );
	}

	public function testShouldAllowTestsToForceADeterministicToken() {
		RenderToken::reset( 'deadbeefcafebabe' );

		$this->assertSame( 'deadbeefcafebabe', RenderToken::get() );
	}
}
