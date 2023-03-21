<?php
require_once("../internal/logic/db.php");

if(!isset($_SESSION['pupil'])){
	echo json_encode(array('status' => "error", 'message' => "Bitte melde dich an, um Dateien hochladen zu können."));
	return exit();
}

$offset = intval($_POST['offset']);
if($offset == 0){
    $_SESSION[basename($_FILES['file']['name'])] = rand(100000,100000000);
}

if(!isset($_SESSION[basename($_FILES['file']['name'])])) $_SESSION[basename($_FILES['file']['name'])] = rand(100000,100000000);
$targetDir = '/media/hdd1/';
$targetFile = $targetDir . $_SESSION['pupil'] . "_" . $_SESSION[basename($_FILES['file']['name'])] . "_" . basename($_FILES['file']['name']);

if (isset($_FILES['file']) && isset($_POST['offset']) && isset($_POST['filesize'])) {
  $offsetFile = file_exists($targetFile) ? filesize($targetFile) : 0;
  if($offset != $offsetFile){
      echo json_encode(array('status' => "position", 'offset' => $offsetFile));
      return exit();
  }

  $fileSize = intval($_POST['filesize']);

  $fileData = file_get_contents($_FILES['file']['tmp_name']);
  $fileHandle = fopen($targetFile, 'a');
  fseek($fileHandle, $offset);
  fwrite($fileHandle, $fileData);
  fclose($fileHandle);

  if ($offset + $_FILES['file']['size'] >= $fileSize) {
    function generateRandomString($length = 16) {
  		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  		$charactersLength = strlen($characters);
  		$randomString = '';
  		for ($i = 0; $i < $length; $i++) {
  			$randomString .= $characters[rand(0, $charactersLength - 1)];
  		}
  		return $randomString;
  	}
  	$code = generateRandomString(64);
    $stmtData = $db->prepare("INSERT INTO ".DBTBL.".files (path, name, type, size, code) VALUES (:path, :name, :type, :size, :code); ");
    $stmtData->execute(array('path' => $targetFile, 'name' => basename($_FILES['file']['name']), 'type' => $_FILES['file']['type'], 'size' => $fileSize, 'code' => $code));

    echo json_encode(array('status' => "complete", 'code' => $code));
  } else {
    echo json_encode(array('status' => "uploading"));
  }
}
?>
