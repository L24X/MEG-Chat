<?php
require_once("../logic/db.php");

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
}

$blog = $_GET['blog'];

$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".blog WHERE id = :id;");
$stmtData->execute(array('id' => $blog));
$row = $stmtData->fetchObject();
$blog_data = (array)$row;

if(isset($_SERVER['HTTP_USER_AGENT'])){
    $is_mobile = preg_match("/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
} else {
	$is_mobile = isset($_COOKIE['desktop']) ? ($_COOKIE['desktop'] == "a") : true;
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MEG Chat | Blog | <?php echo htmlspecialchars($blog_data['header']); ?></title>
        <meta name="description" content="<?php echo htmlspecialchars($blog_data['text']); ?>">
        <meta name="keywords" lang="de" content="meg, max, ernst, gymnasium, chat, online, schueler, chatten, austauschen, hausaufgaben, fragen, blog, artikel, austausch, kontakt">
        <? require('../middleware/head.php'); ?>
    </head>
    <body>
		<?php require('../middleware/navbar.php'); ?>
		<div id="site_container">
			<?php if($is_mobile){ ?>
				<button onclick="window.history.go(-1); " style="position: fixed; top: 85px; left: 10px; min-height: 50px; height: auto; width:  auto; font-size: 16px; background-color: transparent; font-size: 24px; color: white; border: none; outline: none; ">&#8678;</button>
			<?php } ?>
	        <div style="float: left; width: 100%; min-width: 300px; max-width: 100%; text-align: center; margin-top: 100px;" class="centriert">
	            <div style="height: 100%; ">
	            <?php if(!$blog_data){
				    ?>
				    <h1>Dieser Blogbeitrag konnte nicht gefunden werden!</h1>
	                <?php
				} else { ?>
	                <h1><?php echo htmlspecialchars($blog_data['header']); ?></h1>
	                <div style="width: 100%; height: auto; " class="centriert">
	                    <img style="width: 500px; max-width: 100%; " src="<?php echo htmlspecialchars($blog_data['image']); ?>">
	                </div>
	                <div style="width: 100%; height: auto; margin-top: 10px; " class="centriert">
	                    <div style="width: 800px; max-width: 100%; font-size: 20px;"><?php echo htmlspecialchars($blog_data['text']); ?></div>
	                </div>
	            <?php } ?>
	            </div>
	        </div>
	    </div>
    </body>
</html>
