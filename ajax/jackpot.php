<?
require_once("../internal/logic/db.php");

$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".jackpots ORDER BY id DESC LIMIT 1; ");
$stmtData->execute(array('id' => $chat));
$row = $stmtData->fetchObject();
if(!$row) return exit();
if($row){
	$jackpot_data = (array)$row;
}

$stmtData = $db->prepare("SELECT SUM(balance) as balance FROM ".DBTBL.".jackpots_tickets WHERE jackpot = :jackpot; ");
$stmtData->execute(array('jackpot' => $jackpot_data['id']));
$row = $stmtData->fetchObject();
if($row){
	$jackpot_data['balance'] = ((array)$row)['balance'];
}

echo json_encode($jackpot_data);
?>
