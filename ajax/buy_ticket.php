<?
require_once("../internal/logic/db.php");

if(!isset($_SESSION['pupil'])){
	return;
}

if(!isset($_POST['1']) || (!isset($_POST['2']) || (!isset($_POST['3']) || (!isset($_POST['4']) || (!isset($_POST['5']) || (!isset($_POST['6'])){
    return;	
}

$price = 25;

$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
$stmtCheck->execute(array('id' => $_SESSION['pupil']));
$pupil_data = (array)$stmtCheck->fetchObject();

if($pupil_data['money'] < $price){
    echo json_encode(array('error' => "Du hast nicht genug Guthaben. Ein Ticket kostet 25 MEG-Taler."));	
}

$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".jackpots ORDER BY id DESC LIMIT 1; ");
$stmtData->execute();
$row = $stmtData->fetchObject();
if(!$row) return exit();
if($row){
	$jackpot_data = (array)$row;
}

$tipp = array();
$tipp[1] = (int)$_POST['1'];
$tipp[2] = (int)$_POST['2'];
$tipp[3] = (int)$_POST['3'];
$tipp[4] = (int)$_POST['4'];
$tipp[5] = (int)$_POST['5'];
$tipp[6] = (int)$_POST['6'];

$stmtData = $db->prepare("INSERT INTO ".DBTBL.".jackpots_tickets (pupil, jackpot, balance, tipp) VALUES (:pupil, :jackpot, :price, :tipp); UPDATE ".DBTBL.".pupils SET money = pupils.money - :price WHERE id = :pupil;");
$stmtData->execute(array('pupil' => $pupil_data['id'], 'jackpot' => $jackpot_data['id'], 'price' => $price, 'tipp' => json_encode($tipp)));
?>
