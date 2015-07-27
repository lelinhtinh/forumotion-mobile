<?php

function cat_status($val) {
	if ($val === 'forum_new' || $val === 'cat_new') {
		return 'New posts';
	} else if ($val === 'forum_locked' || $val === 'cat_locked') {
		return 'This forum is locked: you cannot post, reply to, or edit topics.';
	} else {
		return 'No new posts';
	}
}

function category($forumrow, $forumsArr) {

	global $forumsObj;
	$forum = '';

	foreach ($forumsArr as $k) {

		$v = $forumsObj->$k;

		$item = str_replace('{catrow.forumrow.U_VIEWFORUM}', 'viewforum.php?selected_id=' . $k, $forumrow);
		$item = str_replace('{catrow.forumrow.L_FORUM_FOLDER_ALT}', cat_status($v->{'status'}), $item);
		$item = str_replace('{catrow.forumrow.FOLDER_CLASSNAME}', $v->{'status'}, $item);
		$item = str_replace('{catrow.forumrow.LEVEL}', '3', $item);
		$item = str_replace('{catrow.forumrow.FORUM_NAME}', $v->{'name'}, $item);
		$item = str_replace('{catrow.forumrow.POSTS}', $v->{'posts'}, $item);
		$item = str_replace('{L_POSTS}', ' Posts', $item);
		$item = str_replace('{L_IN}', '  ', $item);
		$item = str_replace('{catrow.forumrow.TOPICS}', $v->{'topics'}, $item);
		$item = str_replace('{L_TOPICS}', ' Topics', $item);
		$item = str_replace('{catrow.forumrow.L_LATEST_POST_FROM_THE}', $v->{'lastpost'}, $item);
		$forum .= $item;
	}

	return $forum;
}

function information($text) {
	global $path;

	$information = file_get_contents('../templates/' . $path . '/message_body.php');
	$information = str_replace('<!-- BEGIN spacer --><br /><!-- END spacer -->', '<!-- Spacer -->', $information);
	$information = str_replace('{MESSAGE_TITLE}', 'Information', $information);
	$information = str_replace('{MESSAGE_TEXT}', $text, $information);

	echo $information;
}

$topics_list = file_get_contents('../templates/' . $path . '/topics_list_box.php');

function tempTopicsList($code, $html) {
	global $topics_list;

	return str_replace('{' . $code . '}', $html, $topics_list);
}

function topics_list() {
	global $selected_id, $topics_list;

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

			$topicsObj = json_decode(file_get_contents('../data/topics.json'));

			$topicrow = $arg[1];
			$txt = '';

			foreach ($topicsObj as $key => $value) {
				$row = $topicrow;
				$status = $value->{'status'};
				$topic_type = '';
				if(preg_match('/_global/', $status)) {
					$topic_type = '<strong>Global announcement:</strong> ';
				} else if(preg_match('/_announce/', $status)) {
					$topic_type = '<strong>Announcement:</strong> ';
				} else if(preg_match('/_sticky/', $status)) {
					$topic_type = '<strong>Sticky:</strong> ';
				}
				if (!empty($value->{'poll'})) {
					$topic_type .= '<strong>[ Poll ]</strong> ';
				}


				$row = str_replace('{topics_list_box.row.L_TITLE}', 'Announcement & Sticky', $row);
				$row = str_replace('{topics_list_box.row.topic.table_sticky.L_TITLE}', 'Topics', $row);
				$row = str_replace('{topics_list_box.row.U_VIEW_TOPIC}', 'viewtopic.php', $row);
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

	return $topics_list;

}

?>