<?
require_once("../internal/logic/db.php");

if(!isset($_SESSION['pupil'])){
	return;
}

$chat = $_POST['chat'];

$message_type = "text";
if(isset($_POST['type'])){
    $message_type = $_POST['type'];
}

$chat_data = false;
$member = false;

$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".chats WHERE id = :id; ");
$stmtData->execute(array('id' => $chat));
$row = $stmtData->fetchObject();
if($row){
	$chat_data = (array)$row;
}
if(!$chat_data) return;

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

$stmtMessage = $db->prepare("INSERT INTO ".DBTBL.".chats_messages (chat, author, text, type) VALUES (:chat, :author, :text, :type); ");
$stmtMessage->execute(array('chat' => $chat_data['id'], 'author' => $member['pupil'], 'text' => $_POST['text'], 'type' => $message_type));

$stmtInsert = $db->prepare("UPDATE ".DBTBL.".pupils SET money = ".DBTBL.".pupils.money + (SELECT COUNT(id) AS count FROM ".DBTBL.".chats_members WHERE chat = :chat) -1 WHERE id = :id; ");
$stmtInsert->execute(array('chat' => $chat_data['id'], 'id' => $_SESSION['pupil']));
?>
