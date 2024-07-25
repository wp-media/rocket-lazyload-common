<?php

namespace RocketLazyload\Tests\Unit;

use ReflectionObject;
use WPMedia\PHPUnit\Unit\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	protected $config;

	protected function set_up() {
        parent::set_up();

		if ( empty( $this->config ) ) {
			$this->loadTestDataConfig();
		}
    }

	protected function tear_down() {
        parent::tear_down();
    }

	public function configTestData() {
		if ( empty( $this->config ) ) {
			$this->loadTestDataConfig();
		}

		return isset( $this->config['test_data'] )
			? $this->config['test_data']
			: $this->config;
	}

	protected function loadTestDataConfig() {
		$obj      = new ReflectionObject( $this );
		$filename = $obj->getFileName();

		$this->config = $this->getTestData( dirname( $filename ), basename( $filename, '.php' ) );
	}
}
