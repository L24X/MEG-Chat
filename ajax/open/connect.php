<?php

require_once("../../internal/logic/db.php");

if (isset($_GET['send'])) {
	if(!isset($_GET['data'])) return;
	$stmtMessage = $db->prepare("INSERT INTO meg2.data (data, channel) VALUES (:data, :channel); ");
    $stmtMessage->execute(array('channel' => isset($_GET['channel']) ? $_GET['channel'] : 0, 'data' => json_encode($_GET['data'])));
} else {
	$stmtMessage = $db->prepare("SELECT * FROM meg2.data WHERE id > :last AND channel = :channel; ");
    $stmtMessage->execute(array('channel' => isset($_GET['channel']) ? $_GET['channel'] : 0, 'last' => isset($_GET['last']) ? $_GET['last'] : -1));
    $data = array();
    while($row = $stmtMessage->fetchObject()){
		$data[] = array('data' => json_decode(((array)$row)['data']), 'id' => ((array)$row)['id']);
	}
    echo json_encode($data);
}

?>
