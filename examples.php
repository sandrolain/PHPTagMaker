<?php

require_once __DIR__ . '/TagMaker/N.php';

$data = ['div#my-id',
	['div.my-classname.cls-2',
		[
			'title'	=> 'Title attr.',
			'data-custom'	=> '123456',
			'data-json'		=> ['one' => 1, 'two' => 2, 'three' => 3],
			'class'			=> "class-3"
		],
		'Text string ', ['br'], ['b', 'Bold text'], '<br/>', '&amp;scaped string',
		['<div id="test-id">',
			['<i>', 'Italic text'],
			['br'],
			['img', ['src' => 'https://dummyimage.com/640x200/000/fff.png']]
		]
	]
];

$html = \TagMaker\N($data);

echo '<style type="text/css">

body {
	padding: 20px;
	margin: 0;
	font-family: Verdana, Helvetica, sans-serif;
}

pre {
	padding: 20px;
	border: 1px solid #999;
	word-wrap: break-word;
	white-space: pre-wrap;
}

</style>';
echo '<h3>PHP Structure</h3><pre>' . htmlspecialchars(var_export($data, TRUE)) . '</pre>';
echo '<h3>HTML Output</h3><pre>' . htmlspecialchars($html) . '</pre>';
echo '<h3>Output</h3>' . $html;