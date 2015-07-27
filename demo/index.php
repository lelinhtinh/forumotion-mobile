<?php

require_once '../config.php';
require_once '../functions.php';

// HEADER
include 'inc/header.php';

// INDEX_BOX
$index_box = file_get_contents('../templates/' . $path . '/index_box.php');

$categoriesObj = json_decode(file_get_contents('../data/categories.json'));
$forumsObj = json_decode(file_get_contents('../data/forums.json'));
$forumsArr;

$index_box = preg_replace_callback('/<\!\-\-\sBEGIN\scatrow\s\-\->([\s\S]+)<\!\-\-\sEND\scatrow\s\-\->/', function ($arg) {

	global $categoriesObj, $forumsArr;
	$catrow = $arg[1];
	$txt = '';

	foreach ($categoriesObj as $key => $value) {

		$forumsArr = $value->{'forums'};

		$row = str_replace('{catrow.tablehead.L_FORUM}', $value->{'name'}, $catrow);

		$row = preg_replace_callback('/<\!\-\-\sBEGIN\sforumrow\s\-\->([\s\S]+)<\!\-\-\sEND\sforumrow\s\-\->/', function ($ar) {

			global $forumsArr;
			$forumrow = $ar[1];
			return category($forumrow, $forumsArr);

		}, $row);

		$txt .= $row;
	}

	return $txt;

}, $index_box);

echo $index_box;

// FOOTER
include 'inc/footer.php';

?>
