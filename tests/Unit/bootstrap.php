<?php
/**
 * Bootstraps the Rocket Lazyload Common Unit Tests
 *
 * @package RocketLazyload\Tests\Unit
 */

if (version_compare(phpversion(), '5.6.0', '<')) {
    die('Rocket Lazyload Common Unit Tests require PHP 5.6 or higher.');
}

define('RLL_COMMON_TESTS_ROOT', __DIR__);
define('RLL_COMMON_ROOT', dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

$rll_common_autoload_path = RLL_COMMON_ROOT . 'vendor/';

if (! file_exists($rll_common_autoload_path . 'autoload.php')) {
    die('Whoops, we need Composer before we start running tests.  Please type: `composer install`.  When done, try running `phpunit` again.');
}

require_once $rll_common_autoload_path . 'autoload.php';
unset($rll_common_autoload_path);
