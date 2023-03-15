<?
require_once("../internal/logic/db.php");

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
}

$chat = $_POST['chat'];

$chat_data = false;
$member = false;

$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".chats WHERE id = :id; ");
$stmtData->execute(array('id' => $chat));
$row = $stmtData->fetchObject();
if($row){
	$chat_data = (array)$row;
}

if($chat_data){
	if(isset($_SESSION['pupil'])){
		$stmtMember = $db->prepare("SELECT * FROM ".DBTBL.".chats_members WHERE pupil = :pupil AND chat = :chat;");
	    $stmtMember->execute(array('pupil' => $_SESSION['pupil'], 'chat' => $chat_data['id']));
	    if($member = $stmtMember->fetchObject()){
			$member = (array)$member;
		} else {
			$stmtInsertMember = $db->prepare("INSERT INTO ".DBTBL.".chats_members (pupil, chat) VALUES (:pupil, :chat);");
		    $stmtInsertMember->execute(array('pupil' => $_SESSION['pupil'], 'chat' => $chat_data['id']));
		    
		    $stmtMember = $db->prepare("SELECT * FROM ".DBTBL.".chats_members WHERE pupil = :pupil AND chat = :chat;");
		    $stmtMember->execute(array('pupil' => $_SESSION['pupil'], 'chat' => $chat_data['id']));
		    if($member = $stmtMember->fetchObject()){
				$member = (array)$member;
			}
		}
	}
	if($chat_data['public'] != 1){
		if(!$member) $chat_data = false;
	}
}

if(!$chat_data) return;

$stmtMessage = $db->prepare("SELECT * FROM (SELECT * FROM ".DBTBL.".chats_messages WHERE chat = :chat AND id > :last ORDER BY id DESC LIMIT 255) ORDER BY id ASC; ");
$stmtMessage->execute(array('chat' => $chat_data['id'], 'last' => $_POST['last']));

$messages = array();
$old_last_id = -1;
if($member) $old_last_id = $member['last_readed_message'];
$last_id = $old_last_id;
while($m = $stmtMessage->fetchObject()){
	$m = (array)$m;
	$stmtAuthor = $db->prepare("SELECT id, fullname as username, avatar FROM ".DBTBL.".pupils WHERE id = :pupilId;");
	$stmtAuthor->execute(array('pupilId' => $m['author']));
	if($stmtAuthor->rowCount() == 0) continue;
	$last_id = $m['id'];
	
	$messages[] = array('text' => $m['text'], 'time' => $m['time'], 'id' => $m['id'], 'author' => (array)$stmtAuthor->fetchObject(), 'new' => ($m['id'] > $old_last_id));
}
if($member){
    $stmtMember = $db->prepare("UPDATE ".DBTBL.".chats_members SET last_readed_message = :last WHERE id = :id;");
    $stmtMember->execute(array('id' => $member['id'], 'last' => $last_id));
}
echo json_encode($messages);
?>
