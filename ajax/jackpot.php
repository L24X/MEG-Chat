<?
require_once("../internal/logic/db.php");

$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".jackpots ORDER BY id DESC LIMIT 1; ");
$stmtData->execute();
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

$jackpot_data['tickets'] = array();

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
	
	$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".jackpots_tickets WHERE jackpot = :jackpot AND pupil = :id; ");
    $stmtData->execute(array('jackpot' => $jackpot_data['id'], 'id' => $pupil_data['id']));
    
    while($row = $stmtData->fetchObject()){
		$ticket_data = (array)$row;
		$jackpot_data['tickets'][] = array('id' => $ticket_data['id'], 'numbers' => json_decode($ticket_data['tipp']));
	}
}


echo json_encode($jackpot_data);
?>
