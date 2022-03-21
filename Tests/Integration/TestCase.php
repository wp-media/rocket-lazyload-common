<?php

namespace RocketLazyload\Tests\Integration;

use WPMedia\PHPUnit\Integration\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	public function set_up() {
		parent::set_up();
	}

	public function tear_down() {
		parent::tear_down();
	}
}
