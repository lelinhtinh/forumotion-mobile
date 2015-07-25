<?php

require_once('../config.php');

// HEADER
$header = file_get_contents('../templates/' . $path . '/overall_header.php');

function tempHeader($code, $html) {
    global $header;
    return str_replace('{' . $code . '}', $html, $header);
}

function blockHeader($code, $html) {
    global $header;
    return preg_replace('/<\!\-\-\sBEGIN\s' . $code . '\s\-\->([\s\S]+)<\!\-\-\sEND\s' . $code . '\s\-\->/', $html, $header);
}

$header = tempHeader('L_LANG_HTML', $lang);
$header = tempHeader('SITENAME_TITLE', $sitename);
$header = tempHeader('PAGE_TITLE', '');
$header = tempHeader('S_CONTENT_ENCODING', $charset);
$header = blockHeader('switch_canonical_url', '<!-- Canonical -->');
$header = tempHeader('META', '<!-- META -->');
$header = tempHeader('T_HEAD_STYLESHEET', '<link href="../style/' . $path . '.css" rel="stylesheet" type="text/css" />');
$header = tempHeader('META_FAVICO', '<link rel="shortcut icon" type="image/x-icon" href="' . $ico . '">');
$header = tempHeader('SITENAME', $sitename);
$header = tempHeader('URL_BOARD_DIRECTORY', 'http://www.board-directory.net');
$header = tempHeader('SEARCH_FORUMS', 'Search forums');
$header = tempHeader('JQUERY_PATH', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
$header = tempHeader('JS_DIR', 'http://illiweb.com/rsc/14/frm/lang/');
$header = tempHeader('L_LANG', $lang);
$header = blockHeader('google_analytics_code', '<!-- Google Analytics -->');
$header = tempHeader('HOSTING_JS', '<script src="99123.js" type="text/javascript"></script>' . "\n" .
"\t" . '<script type="text/javascript" src="http://illiweb.com/rsc/14/frm/mobile/navigationBar/mobileNavbar.js"></script>' . "\n" .
"\t" . '<script type="text/javascript">' . "\n" .
"\t" . '//<![CDATA[' . "\n" .
"\t" . 'var _userdata = new Object();' . "\n" .
"\t\t" . '_userdata["session_logged_in"] = 1;' . "\n" .
"\t\t" . '_userdata["page_desktop"] = "index.forum?mobile&redirect=%2F";' . "\n" .
"\t\t" . '_userdata["page_login"] = "/login";' . "\n" .
"\t\t" . '_userdata["page_logout"] = "/login?logout=1";' . "\n" .
"\t\t" . '_userdata["page_home"] = "/";' . "\n" .
"\t" . 'var _lang = new Object();' . "\n" .
"\t\t" . '_lang["Desktop"] = "Classic version";' . "\n" .
"\t\t" . '_lang["Home"] = "Home";' . "\n" .
"\t\t" . '_lang["Login"] = "Log in";' . "\n" .
"\t\t" . '_lang["Logout"] = "Log out";' . "\n" .
"\t" . '$(document).ready(mobileNavbar.init)' . "\n" .
"\t" . 'if(typeof(_userdata) == "undefined")' . "\n" .
"\t" . 'var _userdata = new Object();' . "\n" .
"\t\t" . '_userdata["session_logged_in"] = 1;' . "\n" .
"\t\t" . '_userdata["username"] = "Zzbaivong";' . "\n" .
"\t\t" . '_userdata["user_id"] = 1;' . "\n" .
"\t\t" . '_userdata["user_level"] = 1;' . "\n" .
"\t\t" . '_userdata["user_lang"] = "en";' . "\n" .
"\t\t" . '_userdata["activate_toolbar"] = 0;' . "\n" .
"\t\t" . '_userdata["fix_toolbar"] = 0;' . "\n" .
"\t\t" . '_userdata["notifications"] = 1;' . "\n" .
"\t\t" . '_userdata["avatar"] = "<img src=\"http://r28.imgfast.net/users/2816/63/36/82/avatars/1-2.png\" alt=\"\" />";' . "\n" .
"\t\t" . '_userdata["user_posts"] = 1193;' . "\n" .
"\t\t" . '_userdata["user_nb_privmsg"] = 586;' . "\n" .
"\t\t" . '_userdata["point_reputation"] = 2780;' . "\n" .            
"\t" . '//]]>' . "\n" .
"\t" . '</script>' . "\n" .
"\t" . '<!-- BEGIN Ads -->' . "\n" .
"\t" . '<style type="text/css">' . "\n" .
"\t" . '#mpage-body .23fd505, #mpage-body .d4e2892e  {background: none #C4C4C6;}#mpage-body .d4e2892e .banniere {width:320px;/*height:50px;*/background: none #C4C4C6;margin:0 auto;}#mpage-body .23fd505 iframe, #mpage-body .d4e2892e iframe{display: block important!;visibility: visible important!}div#mpage-body iframe[src*=adstune]:not([style*=display]), div#mpage-body iframe[src*=criteo]:not([style*=display]), div#mpage-body iframe[src*=ad6b]:not([style*=display]), div#mpage-body iframe[src*=z5x]:not([style*=display]), div#mpage-body iframe[src*=doubleclick]:not([style*=display]) {display: block !important;visibility: visible !important;}' . "\n" .
"\t" . '</style>' . "\n" .
"\t" . '<!-- END Ads -->');
$header = tempHeader('NAV_CAT_DESC', '<h1 class="mobile_title_content">' . $sitename . '</h1>');
$header = blockHeader('html_validation', '<!-- HTML Validation header -->' . "\n" . '<!-- BEGIN Ads --><div><div class="d4e2892e"><div class="banniere"><div id="global_ad_id1"><div id="m_dcontentglobal_ad_id1" style="display: block;"><img width="320" height="50" style="border-style: none" src="ads.gif"></div></div></div><div class="clear"></div></div><div style="clear:both;"></div></div><!-- END Ads -->' . "\n");

echo $header;



// INDEX_BOX
$index_box = file_get_contents('../templates/' . $path . '/index_box.php');

$categoriesObj = json_decode(file_get_contents('../data/categories.json'));
$forumsObj = json_decode(file_get_contents('../data/forums.json'));
$forumsArr;

$index_box = preg_replace_callback('/<\!\-\-\sBEGIN\scatrow\s\-\->([\s\S]+)<\!\-\-\sEND\scatrow\s\-\->/', function($arg) {    
    
    global $categoriesObj;
    global $forumsArr;
    $catrow = $arg[1];
    $txt = '';
    
    foreach ($categoriesObj as $key => $value) {        
        
        $forumsArr = $value->{'forums'};
        
        $row = str_replace('{catrow.tablehead.L_FORUM}', $value->{'name'}, $catrow);
        
        $row = preg_replace_callback('/<\!\-\-\sBEGIN\sforumrow\s\-\->([\s\S]+)<\!\-\-\sEND\sforumrow\s\-\->/', function($ar) {
            
            global $forumsObj;
            global $forumsArr;
            $frow = $ar[1];
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
            
        }, $row);
        
        $txt .= $row;
    }
    
    return $txt;
    
}, $index_box);

echo $index_box;



// FOOTER
$footer = file_get_contents('../templates/' . $path . '/overall_footer.php');

$footer = str_replace('{PROTECT_FOOTER}', '<!-- PROTECT FOOTER -->', $footer);
$footer = preg_replace('/<\!\-\-\sBEGIN\shtml_validation\s\-\->([\s\S]+)<\!\-\-\sEND\shtml_validation\s\-\->/', '<!-- HTML Validation footer -->', $footer);

echo $footer;
    
?>
