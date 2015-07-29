<?php

require_once '../config.php';
require_once '../functions.php';

$index_box = file_get_contents('../templates/' . $path . '/index_box.php');
$categoriesObj = json_decode(file_get_contents('data/categories.json'));
$forumsObj = json_decode(file_get_contents('data/forums.json'));

$selected_id = isset($_GET['selected_id']) ? $_GET['selected_id'] : '';
$has_forum;
if (preg_match('/^f(1|2|3|4|5|6|7|8|9|10|11)$/', $selected_id)) {
	$page_title = $forumsObj->$selected_id->{'name'};
	$navtitle = $page_title;
	$category = $forumsObj->$selected_id->{'category'};
	if (preg_match('/^f\d+$/', $category)) {
		if ($robot) {
			$backlink = $category . '-forum.html';
		} else {
			$backlink = 'viewforum.php?selected_id=' . $category;
		}
	} else {
		if ($robot) {
			$backlink = $indexurl . 'preview';
		} else {
			$backlink = $indexurl . 'translate';
		}
	}
	$has_forum = true;
} else if (preg_match('/^c(1|2|3)$/', $selected_id)) {
	$page_title = $categoriesObj->$selected_id->{'name'};
	$navtitle = $page_title;
	if ($robot) {
		$backlink = $indexurl . 'preview';
	} else {
		$backlink = $indexurl . 'translate';
	}
	$has_forum = true;
} else {
	$page_title = 'Information';
	$has_forum = false;
}

// HEADER
include 'inc/header.php';

// TOPICS LIST
if ($has_forum) {

	$topics_list = file_get_contents('../templates/' . $path . '/topics_list_box.php');

	function tempTopicsList($code, $html) {
		global $topics_list;

		return str_replace('{' . $code . '}', $html, $topics_list);
	}

	$viewforum = file_get_contents('../templates/' . $path . '/viewforum_body.php');
	$dataObj = $categoriesObj;
	$dataSub = 'forums';
	if (preg_match('/^f\d+$/', $selected_id)) {
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
	if (preg_match('/^f\d+$/', $selected_id)) {

		$topics_list = preg_replace('/<\!\-\-\sBEGIN\smulti_selection\s\-\->([\s\S]+?)<\!\-\-\sEND\smulti_selection\s\-\->/', '<!-- Multi selection -->', $topics_list);

		$topics_list = preg_replace('/<\!\-\-\sBEGIN\sheader_row\s\-\->([\s\S]+)<\!\-\-\sEND\sheader_row\s\-\->/', '<!-- Header row -->', $topics_list);
		$topics_list = str_replace('<!-- BEGIN spacer --><br /><!-- END spacer -->', '<!-- Spacer -->', $topics_list);

		if (preg_match('/^f(6|11)$/', $selected_id)) {

			$topics_list = preg_replace('/<\!\-\-\sBEGIN\stopic\s\-\->([\s\S]+)<\!\-\-\sEND\stopic\s\-\->/', '<!-- Topic -->', $topics_list);
			$topics_list = tempTopicsList('topics_list_box.row.L_TITLE', 'Topics');
			$topics_list = tempTopicsList('topics_list_box.row.L_NO_TOPICS', 'No message.');

		} else {

			$topics_list = preg_replace('/<\!\-\-\sBEGIN\sno_topics\s\-\->([\s\S]+)<\!\-\-\sEND\sno_topics\s\-\->/', '<!-- No topic -->', $topics_list);

			$topics_list = preg_replace_callback('/<\!\-\-\sBEGIN\srow\s\-\->([\s\S]+)<\!\-\-\sEND\srow\s\-\->/', function ($arg) {
				global $robot;
				$topicsObj = json_decode(file_get_contents('../translate/data/topics.json'));

				$topicrow = $arg[1];
				$txt = '';

				foreach ($topicsObj as $key => $value) {
					$row = $topicrow;
					$status = $value->{'status'};
					$topic_type = '';
					if (preg_match('/_global/', $status)) {
						$topic_type = '<strong>Global announcement:</strong> ';
					} else if (preg_match('/_announce/', $status)) {
						$topic_type = '<strong>Announcement:</strong> ';
					} else if (preg_match('/_sticky/', $status)) {
						$topic_type = '<strong>Sticky:</strong> ';
					}
					if (!empty($value->{'poll'})) {
						$topic_type .= '<strong>[ Poll ]</strong> ';
					}

					$row = str_replace('{topics_list_box.row.L_TITLE}', 'Announcement & Sticky', $row);
					$row = str_replace('{topics_list_box.row.topic.table_sticky.L_TITLE}', 'Topics', $row);
					if ($robot) {
						$row = str_replace('{topics_list_box.row.U_VIEW_TOPIC}', $key . '-topic.html', $row);
					} else {
						$row = str_replace('{topics_list_box.row.U_VIEW_TOPIC}', 'viewtopic.php?t=' . substr($key, 1), $row);
					}
					$row = str_replace('{topicrow.TOPIC_FOLDER_IMG_ALT}', $status, $row);
					$row = str_replace('{topics_list_box.row.FOLDER_CLASSNAME}', $status, $row);
					$row = str_replace('{topics_list_box.row.TOPIC_TYPE}', $topic_type, $row);
					$row = str_replace('{topics_list_box.row.TOPIC_TITLE}', $value->{'name'}, $row);
					$row = str_replace('{topics_list_box.row.REPLIES}', $value->{'replies'}, $row);
					$row = str_replace('{L_REPLIES}', ' Replies', $row);
					$row = str_replace('{L_IN}', '  ', $row);
					$row = str_replace('{topics_list_box.row.VIEWS}', $value->{'views'}, $row);
					$row = str_replace('{L_VIEWS}', ' Views', $row);
					$row = str_replace('{topics_list_box.row.L_LATEST_POST_FROM_THE}', $value->{'lastpost'}, $row);

					if ($key !== 't1') {
						$row = preg_replace('/<\!\-\-\sBEGIN\sheader_table\s\-\->[\s\S]+<\!\-\-\sEND\sheader_table\s\-\->/', '<!-- Header table -->', $row);
					}
					if ($key !== 't7') {
						$row = preg_replace('/<\!\-\-\sBEGIN\stable_sticky\s\-\->[\s\S]+<\!\-\-\sEND\stable_sticky\s\-\->/', '<!-- Table sticky -->', $row);
					}
					if ($key !== 't13') {
						$row = preg_replace('/<\!\-\-\sBEGIN\sbottom\s\-\->[\s\S]+<\!\-\-\sEND\sbottom\s\-\->/', '<!-- Bottom -->', $row);
					}

					$txt .= $row;
				}

				return $txt;

			}, $topics_list);

		}

		$viewforum = str_replace('{TOPICS_LIST_BOX}', $topics_list, $viewforum);
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