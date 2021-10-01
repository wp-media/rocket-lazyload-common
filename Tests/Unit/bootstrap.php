<?php
/**
 * Initializes the wp-media/phpunit handler, which then calls the rocket integration test suite.
 */

define( 'WPMEDIA_PHPUNIT_ROOT_DIR', dirname( __DIR__, 2 ) . DIRECTORY_SEPARATOR );
define( 'WPMEDIA_PHPUNIT_ROOT_TEST_DIR', __DIR__ );
define( 'RLL_COMMON_ROOT', dirname( __DIR__, 2 ) . DIRECTORY_SEPARATOR );
define( 'RLL_COMMON_TESTS_ROOT', __DIR__ . DIRECTORY_SEPARATOR . 'Unit' );

require_once WPMEDIA_PHPUNIT_ROOT_DIR . 'vendor/wp-media/phpunit/Unit/bootstrap.php';
