<?php

require_once '../config.php';
require_once '../functions.php';

$index_box = file_get_contents('../templates/' . $path . '/index_box.php');
$categoriesObj = json_decode(file_get_contents('../data/categories.json'));
$forumsObj = json_decode(file_get_contents('../data/forums.json'));

$selected_id = isset($_GET['selected_id']) ? $_GET['selected_id'] : '';
$has_forum;
if (preg_match('/^f(1|2|3|4|5|6|7|8|9|10|11)$/', $selected_id)) {
	$page_title = $forumsObj->$selected_id->{'name'};
	$navtitle = $page_title;
	$category = $forumsObj->$selected_id->{'category'};
	if (preg_match('/^f\d+$/', $category)) {
		$backlink = 'viewforum.php?selected_id=' . $category;
	} else {
		$backlink = $indexurl;
	}
	$has_forum = true;
} else if(preg_match('/^c(1|2|3)$/', $selected_id)) {
	$page_title = $categoriesObj->$selected_id->{'name'};
	$navtitle = $page_title;
	$backlink = $indexurl;
	$has_forum = true;
} else {
	$page_title = 'Information';
	$has_forum = false;
}

// HEADER
include 'inc/header.php';

if ($has_forum) {
	$viewforum = file_get_contents('../templates/' . $path . '/viewforum_body.php');
	$dataObj = $categoriesObj;
	$dataSub = 'forums';
	if(preg_match('/^f\d+$/', $selected_id)) {
		$dataObj = $forumsObj;
		$dataSub = 'subforums';
	}
	if (isset($dataObj->$selected_id->{$dataSub})) {

		$row = str_replace('{catrow.tablehead.L_FORUM}', $dataObj->$selected_id->{'name'}, $index_box);
		$row = preg_replace_callback('/<\!\-\-\sBEGIN\sforumrow\s\-\->([\s\S]+)<\!\-\-\sEND\sforumrow\s\-\->/', function ($ar) {
			global $dataObj, $dataSub, $selected_id;
			$forumsArr = $dataObj->$selected_id->{$dataSub};
			$forumrow = $ar[1];
			return category($forumrow, $forumsArr);

		}, $row);

		$viewforum = str_replace('{BOARD_INDEX}', $row, $viewforum);
	} else {
		$viewforum = str_replace('{BOARD_INDEX}', '', $viewforum);
	}
	if(preg_match('/^f\d+$/', $selected_id)) {
		$viewforum = str_replace('{TOPICS_LIST_BOX}', topics_list(), $viewforum);
	} else {
		$viewforum = str_replace('{TOPICS_LIST_BOX}', '', $viewforum);
	}

	if (preg_match('/^c\d+$/', $selected_id) || preg_match('/^f(6|11)$/', $selected_id)) {
		$viewforum = str_replace('{PAGINATION}', '', $viewforum);
	} else {
		$viewforum = str_replace('{PAGINATION}', '<div class="mobile_title"><a href="javascript:;" class="mobile_prev_button block"><p>Prev.</p></a><p class="mobile_title_content">Page <strong>9</strong> of <strong>99</strong></p><a href="javascript:;" class="mobile_next_button block"><p>Next</p></a></div>', $viewforum);
	}
	if (preg_match('/^c\d+$/', $selected_id) || preg_match('/^f(6|7|11)$/', $selected_id)) {
		$viewforum = preg_replace('/<\!\-\-\sBEGIN\sswitch_user_authpost\s\-\->([\s\S]+?)<\!\-\-\sEND\sswitch_user_authpost\s\-\->/', '<!-- Switch user authpost -->', $viewforum);
	} else {
		$viewforum = str_replace('{U_POST_NEW_TOPIC}', 'post.php', $viewforum);
		$viewforum = str_replace('{L_NEW_TOPIC}', 'New topic', $viewforum);
	}

	echo $viewforum;
} else {
	information('The forum you selected does not exist.');
}

// FOOTER
include 'inc/footer.php';

?>