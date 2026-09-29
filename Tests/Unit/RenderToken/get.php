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
		( new RenderToken() )->reset();
		parent::tear_down();
	}

	public function testShouldGenerateAHexTokenOfAtLeast16Characters() {
		( new RenderToken() )->reset();

		$token = ( new RenderToken() )->get();

		$this->assertMatchesRegularExpression( '/^[0-9a-f]{16,}$/', $token );
	}

	public function testShouldReturnTheSameTokenOnEveryCallWithinOneRequest() {
		( new RenderToken() )->reset();

		$first  = ( new RenderToken() )->get();
		$second = ( new RenderToken() )->get();

		$this->assertSame( $first, $second );
	}

	public function testShouldReturnADifferentTokenAfterANewRequestResetsIt() {
		( new RenderToken() )->set( 'aaaaaaaaaaaaaaaa' );
		$before = ( new RenderToken() )->get();

		( new RenderToken() )->reset();
		$after = ( new RenderToken() )->get();

		$this->assertNotSame( $before, $after );
	}

	public function testShouldAllowTestsToForceADeterministicToken() {
		( new RenderToken() )->set( 'deadbeefcafebabe' );

		$this->assertSame( 'deadbeefcafebabe', ( new RenderToken() )->get() );
	}
}
