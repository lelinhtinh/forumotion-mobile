<?php

require_once 'config.php';

$file = 'config.php';
$current = file_get_contents($file);
$current = str_replace('$robot = false;', '$robot = true;', $current);
file_put_contents($file, $current);

$categoriesObj = json_decode(file_get_contents('translate/data/categories.json'));
$forumsObj = json_decode(file_get_contents('translate/data/forums.json'));

if (copy('http://localhost/forumotion-mobile/translate/index.php', 'preview/index.html')) {
	echo '<a href="' . $indexurl . 'preview">index</a><br>';
}
echo '<hr>';
foreach ($categoriesObj as $key => $value) {
	if (copy('http://localhost/forumotion-mobile/translate/viewforum.php?selected_id=' . $key, 'preview/' . $key . '-category.html')) {
		echo '<a href="' . $indexurl . 'preview/' . $key . '-category.html">' . $value->{'name'} . '</a><br>';
	}
}
echo '<hr>';
foreach ($forumsObj as $k => $v) {
	if (copy('http://localhost/forumotion-mobile/translate/viewforum.php?selected_id=' . $k, 'preview/' . $k . '-forum.html')) {
		echo '<a href="' . $indexurl . 'preview/' . $k . '-forum.html">' . $v->{'name'} . '</a><br>';
	}
}

$current = str_replace('$robot = true;', '$robot = false;', $current);
file_put_contents($file, $current);

$zip = new ZipArchive();
$filename = 'invision.' . time() . '.baivong.github.io.zip';
if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
	exit('Cannot open <$filename>');
}

$dir = 'templates/' . $path;
$files = scandir($dir);
$size = sizeof($files);

$arr_id = ['0', '0', '06', '07', '08', '09', '10', '11', '12', '13', '14', '01', '02', '03', '04', '05'];

for ($i = 2; $i < $size; $i++) {
	$zip->addFromString('mobile/10' . $arr_id[$i] . '.' . explode('.', $files[$i])[0] . '.txt', file_get_contents('templates/' . $path . '/' . $files[$i]));
}

echo '<hr>';
echo $filename . ' - ' . $zip->numFiles . ' files';
$zip->close();

?>