<?php

function category($frow, $forumsArr) {
    $forumsObj = json_decode(file_get_contents('../data/forums.json'));
    $forum = '';

    foreach ($forumsArr as $k) {

        $v = $forumsObj->$k;

        $item = str_replace('{catrow.forumrow.U_VIEWFORUM}', 'viewforum.php?selected_id=' . $k, $frow);
        $item = str_replace('{catrow.forumrow.L_FORUM_FOLDER_ALT}', $v->{'status'}, $item);
        $item = str_replace('{catrow.forumrow.FOLDER_CLASSNAME}', $v->{'status'}, $item);
        $item = str_replace('{catrow.forumrow.LEVEL}', '3', $item);
        $item = str_replace('{catrow.forumrow.FORUM_NAME}', $v->{'name'}, $item);
        $item = str_replace('{catrow.forumrow.POSTS}', $v->{'posts'}, $item);
        $item = str_replace('{L_POSTS}', 'Posts', $item);
        $item = str_replace('{L_IN}', '  ', $item);
        $item = str_replace('{catrow.forumrow.TOPICS}', $v->{'topics'}, $item);
        $item = str_replace('{L_TOPICS}', 'Topics', $item);
        $item = str_replace('{catrow.forumrow.L_LATEST_POST_FROM_THE}', $v->{'lastpost'}, $item);
        $forum .= $item;
    }

    return $forum;
}

?>