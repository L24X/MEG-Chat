<?php
require_once("../internal/logic/db.php");

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
}

if(isset($_SERVER['HTTP_USER_AGENT'])){
    $is_mobile = preg_match("/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
} else {
	$is_mobile = isset($_COOKIE['desktop']) ? ($_COOKIE['desktop'] == "a") : true;
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="format-detection" content="telephone=no">
	<meta name="msapplication-tap-highlight" content="no">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=0, width=device-width, viewport-fit=cover">
	<link rel="apple-touch-icon" href="appicon.png" />
	<link rel="icon" href="favicon.ico" />
	<title>Candy Crush</title>
	<style type="text/css">html, body { touch-action: auto; position: fixed; } html, body, canvas { -webkit-tap-highlight-color: rgba(0,0,0,0); touch-action: none; } </style>
	<!-- custom-html-head.template.html -->
    <?php require('../internal/middleware/head.php'); ?>
</head>
<body oncontextmenu="return false;" style="position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px;">
<?php require('../internal/middleware/navbar.php'); ?>
		<div id="site_container" style="position: fixed; top: 80px; left:0px; right: 0px; bottom: 0px; ">

<style type="text/css">
.preloader_container {
	z-index: 100000;
	display: flex;
	justify-content: center;
	align-items: center;
	position: fixed;
	top: 80px;
	left: 0;
	width: 100vw;
	height: 100vh;
}
/*noinspection CssUnusedSymbol*/
.preloader_container.preloader_container_hidden {
	display: none;
}
.preloader_spinner {
	border: 16px solid #776bc7; /* Light grey */
	border-top: 16px solid #2e0090; /* Blue */
	border-radius: 50%;
	max-width: 50vh;
	max-height: 50vh;
	width: 64px;
	height: 64px;
	animation: spin 2s linear infinite;
}
.preloader_text {
	color: #776bc7;
	margin-top: 0.5em;
	font: 14px Arial;
}
@keyframes spin {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
}
/* custom-styles.template.css */

</style>

<div class="preloader_container">
	<div class="preloader">
		<div class="preloader_spinner"></div>
		<div class="preloader_text">Loading game...</div>
	</div>
</div>

<script type="text/javascript">
	function preloader_complete() {
		document.querySelector(".preloader_container").classList.add("preloader_container_hidden")
	}
</script>

<!-- custom-html-body.template.html -->


<script type="text/javascript" src="Candy-Crush-Clone.js" onload="preloader_complete()"></script>

<script type="text/javascript">
(() => {

	let options = { passive: false };
	let lastTouchEnd = 0;
	document.addEventListener("touchstart", (e) => { e.preventDefault(); }, options);
	document.addEventListener('touchmove', (e) => { if (e.scale !== 1) e.preventDefault(); }, options);
	document.addEventListener('touchend', (e) => {
		const now = Date.now();
		if (now - lastTouchEnd <= 300) e.preventDefault();
		lastTouchEnd = now;
	}, options);
})()
</script>
</div>
</body>
</html>
