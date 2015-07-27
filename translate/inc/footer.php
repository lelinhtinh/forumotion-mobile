<?php

$footer = file_get_contents('../templates/' . $path . '/overall_footer.php');

$footer = str_replace('{PROTECT_FOOTER}', '<!-- PROTECT FOOTER -->', $footer);
$footer = preg_replace('/<\!\-\-\sBEGIN\shtml_validation\s\-\->([\s\S]+)<\!\-\-\sEND\shtml_validation\s\-\->/', '<!-- HTML Validation footer -->', $footer);

echo $footer;

?>