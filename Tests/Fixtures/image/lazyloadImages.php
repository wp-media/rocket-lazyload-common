<?php

return [
	'testShouldReturnSameWhenNoImage' => [
		'config'   => [
			'native'          => false,
			'noscript_filter' => true,
		],
		'original' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/noimage.html' ),
		'expected' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/noimage.html' ),
	],
	'testShouldReturnImagesLazyloadedNoNative' => [
		'config'   => [
			'native'          => false,
			'noscript_filter' => true,
		],
		'original' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/images.html' ),
		'expected' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/imageslazyloaded.html' ),
	],
	'testShouldReturnImagesLazyloadedNative' => [
		'config'   => [
			'native'          => true,
			'noscript_filter' => true,
		],
		'original' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/images.html' ),
		'expected' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/imageslazyloadednative.html' ),
	],
	'testShouldReturnImagesLazyloadedNoFallback' => [
		'config'   => [
			'native'          => false,
			'noscript_filter' => false,
		],
		'original' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/images.html' ),
		'expected' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/imageslazyloadednofallback.html' ),
	],
];
