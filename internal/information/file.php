<?php
require_once("../logic/db.php");

$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".files WHERE code = :code; ");
$stmtData->execute(array('code' => $_GET['file']));
$row = $stmtData->fetchObject();
if(!$row){
    http_response_code(404);
    return exit();
}
$file_data = (array)$row;

$path = $file_data['path'];
$type = $file_data['type'];
$size = $file_data['size'];
$filename = basename($path);

$file = fopen($path, 'rb');

if ($file === false) {
    die('Die Datei konnte nicht geöffnet werden.');
}

if (isset($_SERVER['HTTP_RANGE'])) {
    $range = $_SERVER['HTTP_RANGE'];
    $range = str_replace('bytes=', '', $range);
    $range = explode('-', $range);
    $start = intval($range[0]);
    $end = ($range[1] == '') ? ($size - 1) : intval($range[1]);
    header('HTTP/1.1 206 Partial Content');
    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
} else {
    $start = 0;
    $end = $size - 1;
    header('Content-Length: ' . $size);
}

header('Content-Type: ' . $type);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Accept-Ranges: bytes');
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

fseek($file, $start);

$buffer_size = 8192;
while (!feof($file) && ($pos = ftell($file)) <= $end) {
    if ($pos + $buffer_size > $end) {
        $buffer_size = $end - $pos + 1;
    }
    set_time_limit(0);
    echo fread($file, $buffer_size);
    flush();
}

fclose($file);
?>