<?php

require_once __DIR__ . '/../src/autoload.php';

$elDiv	= new \TagMaker\Element('div');
$elDiv->setAttribute("style", "border: 1px solid #000000");

$elImg	= new \TagMaker\Element('img');
$elImg->src	= 'https://dummyimage.com/200x200/F00/fff.png';
$elImg->alt	= 'Test Image';

$elDiv->addChildren($elImg, ['br'], "Simple text");

$elDiv[] = new \TagMaker\Element('hr');
$elDiv[] = "Element array access test";

$style = new \TagMaker\Style();

$bodyStyle = $style->rule('body');
	$bodyStyle->padding = 20;
	$bodyStyle->margin = 0;
	$bodyStyle->fontFamily = "Verdana, Helvetica, sans-serif";

$preStyle = $style->rule('pre');
	$preStyle->padding = 20;
	$preStyle->border = "1px solid #999";
	$preStyle->wordWrap = "break-word";
	$preStyle->whiteSpace = "pre-wrap";
	$preStyle->textAlign = "left";

$divStyle = $style->rule('div');
	$divStyle->textAlign = "center";
	$divStyle->padding = 10;

$divStyle = $style->rule('body > div');
	$divStyle->border = "1px solid #F00";

$style['body > div']['> div'] = [
	'border'	=> "1px solid #00F"
];

$style['@media screen and (max-width: 600px)']['div'] = [
	'padding'		=> 5,
	'border-color'	=> '#0C0 !important'
];

$style['@media screen and (max-width: 600px)']['img'] = [
	"max-width" => '100%'
];

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
		],
		$elDiv,
		$style,
		['pre', $style->makeCSS()]
	]
];

$html = \TagMaker\Maker::makeHTML($data);

echo '<meta
name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0, minimal-ui" />';
echo '<h3>PHP Structure</h3><pre>' . htmlspecialchars(var_export($data, TRUE)) . '</pre>';
echo '<h3>HTML Output</h3><pre>' . htmlspecialchars($html) . '</pre>';
echo '<h3>Output</h3>' . $html;