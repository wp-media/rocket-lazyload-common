<?php

namespace RocketLazyload\Tests\Integration;

use WPMedia\PHPUnit\Integration\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	protected function set_up() {
		parent::set_up();
	}

	protected function tear_down() {
		parent::tear_down();
	}
}
