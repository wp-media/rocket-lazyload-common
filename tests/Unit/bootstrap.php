<?php
/**
 * Bootstraps the Rocket Lazyload Common Unit Tests
 *
 * @package RocketLazyload\Tests\Unit
 */

namespace RocketLazyload\Tests\Unit;

use function RocketLazyload\Tests\init_test_suite;

require_once dirname( dirname( __FILE__ ) ) . '/bootstrap-functions.php';
init_test_suite( 'Unit' );
