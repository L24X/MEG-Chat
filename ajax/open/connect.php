<?php
require_once("../../internal/logic/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$stmtMessage = $db->prepare("INSERT INTO meg2.data (data, channel) VALUES (:data, :channel); ");
    $stmtMessage->execute(array('channel' => isset($_POST['channel']) ? $_POST['channel'] : 0, 'data' => json_encode($_POST['data'])));
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$stmtMessage = $db->prepare("SELECT * FROM meg2.data WHERE id > :last AND channel = :channel; ");
    $stmtMessage->execute(array('channel' => isset($_POST['channel']) ? $_POST['channel'] : 0, 'last' => isset($_POST['last']) ? $_POST['last'] : -1));
    $data = (array)$stmtMessage->fetchAll();
    echo json_encode($data);
}

?>
