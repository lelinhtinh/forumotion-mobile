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

	global $forumsObj, $robot;
	$forum = '';

	foreach ($forumsArr as $k) {

		$v = $forumsObj->$k;

		if ($robot) {
			$item = str_replace('{catrow.forumrow.U_VIEWFORUM}', $k . '-forum.html', $forumrow);
		} else {
			$item = str_replace('{catrow.forumrow.U_VIEWFORUM}', 'viewforum.php?selected_id=' . $k, $forumrow);
		}
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

?>