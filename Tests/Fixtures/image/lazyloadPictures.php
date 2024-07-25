<?php

return [
	'testShouldReturnSameWhenNoPicture' => [
		'config'   => [
			'noscript_filter' => true,
		],
		'original' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/noimage.html' ),
		'expected' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/noimage.html' ),
	],
	'testShouldReturnPicturesLazyloaded' => [
		'config'   => [
			'noscript_filter' => true,
		],
		'original' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/pictures.html' ),
		'expected' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/pictureslazyloaded.html' ),
	],
	'testShouldReturnPicturesLazyloadedNoFallback' => [
		'config'   => [
			'noscript_filter' => false,
		],
		'original' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/pictures.html' ),
		'expected' => file_get_contents( RLL_COMMON_ROOT . 'Tests/Fixtures/Image/pictureslazyloadednofallback.html' ),
	],
];
