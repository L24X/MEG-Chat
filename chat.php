<?
require_once("db.php");

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
}

$chat = $_GET['chat'];

$chat_data = false;
$member = false;

$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".chats WHERE id = :id; ");
$stmtData->execute(array('id' => $chat));
$row = $stmtData->fetchObject();
if($row){
	$chat_data = (array)$row;
}

if($chat_data){
    if($chat_data['public'] != 1){
		$has_access = false;
	    if(isset($_SESSION['pupil'])){
			$stmtMember = $db->prepare("SELECT * FROM ".DBTBL.".chats_members WHERE pupil = :pupil AND chat = :chat;");
		    $stmtMember->execute(array('pupil' => $_SESSION['pupil'], 'chat' => $chat_data['id']));
		    if($member = $stmtMember->fetchObject()){
				$member = (array)$member;
				$has_access = true;	
			}
		}
		if(!$has_access){
			$chat_data = false;
		}
	}
}

if($chat_data && isset($_SESSION['pupil']) && !$member){
	$stmtInsertMember = $db->prepare("INSERT INTO ".DBTBL.".chats_members (pupil, chat) VALUES (:pupil, :chat);");
    $stmtInsertMember->execute(array('pupil' => $_SESSION['pupil'], 'chat' => $chat_data['id']));
    
    $stmtMember = $db->prepare("SELECT * FROM ".DBTBL.".chats_members WHERE pupil = :pupil AND chat = :chat;");
    $stmtMember->execute(array('pupil' => $_SESSION['pupil'], 'chat' => $chat_data['id']));
    if($member = $stmtMember->fetchObject()){
		$member = (array)$member;
	}
}


?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MEG Chat | Chat | <? echo htmlspecialchars($chat_data ? $chat_data['name'] : "Nicht Gefunden"); ?></title>
        <meta name="description" content="<? echo htmlspecialchars($chat_data ? $chat_data['description'] : "Dieser Chat exestiert nicht, oder Sie haben keinen Zugriff darauf!"); ?>">
        <meta name="keywords" lang="de" content="meg, max, ernst, gymnasium, max ernst gymnasium, brühl, chat, online, schueler, chatten, austauschen, hausaufgaben, fragen, blog, artikel, austausch, kontakt, neues">
        <meta name="author" content="Lars Ashauer und Tilo Behnke">
        <meta name="robots" content="index,follow">
        <meta http-equiv="Cache-control" content="public">
        <meta name="format-detection" content="telephone=yes">
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		<link rel="manifest" href="/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
        <script src="/resources/js/script.js"></script>
    </head>
    <body style="background-color: #303030; color: lightgray; ">
        <div style="float: left; width: 540px; max-width: 100%; height: auto; max-height: 100%; overflow-x: hidden; overflow-y: auto; ">
			<div style="width: 100%; height: 145px; margin-top: 20px; ">
			    <div style="width: 50%; height: 100%; float: left; cursor: pointer; " class="centriert" onclick="page_navigate('/');">
			        <div style="width: 100%; height: 100%; " class="centriert"><img style="height: 100%; width: auto; max-width: 100%; " src="/logo.png" alt="MEG Chat Logo"></div>
			    </div>
			    <div style="width: 50%; height: 100%; float: right; " class="centriert">
				    <div style="width: calc( 100% - 20px ); text-align: center; height: auto; ">
		                <?
						if(isset($_SESSION['pupil'])){
							?>
							<h2 style="margin-top: 5px; font-size: 14px; word-wrap: break-word; ">Du bist angemeldet als <? echo htmlspecialchars($pupil_data['fullname']); ?>!</h2>
							<?
							if($pupil_data['activated'] == 0){
								?>
								<p style="color: red; font-size: 10px; ">Dein Account ist noch nicht freigeschaltet worden. Bitte gedulte dich einige Zeit oder Kontaktiere einen Administrator. Wir werden deine Identität Prüfen und den Account anschließend freischalten.</p>
								<?
						    }
							?>
							<div style="width: 100%; height: auto; margin-top: 10px; ">
							    <button onclick="page_navigate('/settings.php');" style="background-color: blue; color: white; font-size: 16px; width: 100%; height: 25px; margin-top: 10px; ">Einstellungen</button>
							    <button onclick="window.location.href='/logout.php';" style="background-color: red; color: white; font-size: 16px; width: 100%; height: 25px; margin-top: 10px; ">Abmelden</button>
							</div>
							<?
		                } else { ?>
							<div style="width: 100%; height: auto; ">
		                        <button onclick="page_navigate('/login.php');" style="width: 100%; height: 25px; ">Anmelden</button>
		                        <button onclick="page_navigate('/register.php');" style="width: 100%; height: 50px; margin-top: 10px; ">Mich als Schüler hinzufügen</button>
		                    </div>
		                <? } ?>
	                </div>
			    </div>
			</div>
            <div style="width: 100%; height: auto; margin-top: 20px; ">
				<div id="all_chats_container">
					<div style="margin-top: 60px; width: 100%; height: auto; " class="public_chats_container" id="public_chats_container">
						<h2>Öffentliche Chattgruppen:</h2>
						<? 
						$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".chats WHERE public = 1; ");
						$stmtData->execute();
						while($row = $stmtData->fetchObject()){
							$row = (array)$row;
							$count = 0;
							if(isset($_SESSION['pupil'])){
								$stmtChat = $db->prepare("SELECT * FROM ".DBTBL.".chats_members WHERE pupil = :pupil AND chat = :chat;");
							    $stmtChat->execute(array('pupil' => $_SESSION['pupil'], 'chat' => $row['id']));
							    $last_readed_message = -1;
							    if($member = $stmtChat->fetchObject()){
									$member = (array)$member;
									$last_readed_message = $member['last_readed_message'];
								}
								$stmtCount = $db->prepare("SELECT COUNT(id) as count FROM ".DBTBL.".chats_messages WHERE chat = :chat AND id > :last AND time > :time;");
							    $stmtCount->execute(array('chat' => $row['id'], 'last' => $last_readed_message, 'time' => $pupil_data['registartion_time']));
							    $count = ((array)$stmtCount->fetchObject())['count'];
							}
							?>
							<div class="chatgruppe" onclick="page_navigate('/chat/<? echo htmlspecialchars($row['id']); ?>');">
							    <div style="width: 100%; min-height: 40px; height: auto; ">
								    <div style="height: auto; width: 100%; min-height: 40px; ">
										<div style="width: calc( 100% - 120px ); ">
								            <h4 style="margin: 0; padding: 0; font-size: 18px; "><? echo htmlspecialchars($row['name']); ?></h4>
								            <h6 style="margin: 0; padding: 0; font-size: 14px; font-weight: small; "><? echo htmlspecialchars($row['description']); ?></h6>
								        </div>
								        <? if($count > 0){ ?>
									    <div style="position: absolute; float: right; min-height: 40px; height: auto; width: 100px; " class="centriert">
									        <div style="height: 90%; width: 80%; background-color: red; color: white; border-radius: 10px; font-size: 24px; " class="centriert"><? echo htmlspecialchars($count); ?></div>
									    </div>
									    <? } ?>
								    </div>
							    </div>
							</div>
			            <? } ?>
					</div>
	            </div>
	        </div>
        </div>
        <div style="float: left; width: calc( 100% - 542px ); min-width: 350px; max-width: 100%; text-align: center; height: 100%; ">
			<? if(!$chat_data){
			    ?>
			    <h1>Entweder dieser Chat exestiert nicht oder zu hast keinen Zugriff darauf. Sollte dieses problem weiterhin auftauchen melde dich bitte bei einem Administrator.!</h1>
			    <?
			} else { ?>
                <h1><? echo htmlspecialchars($chat_data['name']); ?></h1>
                <div style="width: 100%; height: auto; margin-top: 10px; " class="centriert">
                     <h2><? echo htmlspecialchars($chat_data['description']); ?></h2>
                </div>
                <div style="width: 100%; height: calc( 100% - 180px ); min-height: 200px; max-height: 100%; " class="centriert">
                    <div style="height: 100%; min-width: 320px; width: 80%; max-width: 95%; position: relative;">
                        <div style="position: absolute; top: 0px; left: 0px; right: 0px; bottom: 50px; overflow-x: hidden; overflow-y: auto; ">
                            <div style="width: 100%; height: 100px; "></div>
                            <div style="width: 100%; height: auto; " id="chat_inner_data"></div>
                            <div style="width: 100%; height: 100px; "></div>
                        </div>
                        <?
                        if(!$member){
							?>
							<div style="position: absolute; bottom: 0px; right: 0px; left: 0px; min-height: 50px; height: auto; " class="centriert">
							    <p style="color: red; ">Um selber Nachrichten in diesen Chat schreiben zu können, melde dich bitte an oder regestriere dich. Der Zugang ist nur für Schüler des MEGs erlaubt!</p>
							</div>
							<?
						} else {
						    ?>
						    <textarea rows="1" onkeydown="message_input_keydown(event);" id="private_message_text" style="position: absolute; bottom: 0px; right: 0px; left: 0px; height: 30px;  font-size: 24px; text-align: left; resize: none; background-color: transparent; " class="text" placeholder="Meine Nachricht.."></textarea>
						    <?	
						}
						?>
                    </div>
                </div>
            <? } ?>
        </div>
        <? if($chat_data){ ?>
        <script>
            window.last_message_id = -1;

			window.message_input_keydown = function(evt) {
				if(document.getElementById("private_message_text").value.split("\n").length < document.getElementById("private_message_text").rows){
					document.getElementById("private_message_text").rows = document.getElementById("private_message_text").value.split("\n").length;
					document.getElementById("private_message_text").style.height = (document.getElementById("private_message_text").rows*30)+"px";
				}
				
			    evt = evt || window.event;
			    var charCode = evt.keyCode || evt.which;
			    
			    if(!charCode) return;
			    
			    if(evt.shiftKey){
				    if (charCode == 13) {
						if(document.getElementById("private_message_text").rows < 10){
							document.getElementById("private_message_text").rows++;
							document.getElementById("private_message_text").style.height = (document.getElementById("private_message_text").rows*30)+"px";
						}
					}
				} else {
					if (charCode == 13) {
						evt.preventDefault();
						
						var value = document.getElementById("private_message_text").value.trim();
						if(value.length == 0) return;
						
						document.getElementById("private_message_text").value = "";
						document.getElementById("private_message_text").rows = 1;
						document.getElementById("private_message_text").style.height = "30px";
						
						function send_chess_message(){
                            post_request("/send_message.php", {text: value, chat: <? echo htmlspecialchars($chat_data['id']); ?>});
						}
						send_chess_message();
					}
			    }
			};
			
			async function get_messages_data(){
				post_request("/load_new_messages.php", {chat: <? echo htmlspecialchars($chat_data['id']); ?>, last: last_message_id}, function(data){
					setTimeout(function(){
					    get_messages_data();
					}, 50);
					data = JSON.parse(data);
					data.forEach(function(z){
						last_message_id = z.id;
		
						var ne = document.createElement("div");
						ne.style = "width: 100%; margin-top: 10px; height: auto; word-warp: break-word; color: white; text-align: left; float: left; font-size: 12px;";
						var na = document.createElement("u");
						na.innerText = z.author.username;
						na.style = "font-weight: bold; cursor: pointer; ";
						na.onclick = function(){
							
						};
						ne.appendChild(na);
						var nt = document.createElement("span");
						nt.style = "margin-left: 10px; ";
						nt.innerText = "\n"+z.text;
						ne.appendChild(nt);
						
						document.getElementById("chat_inner_data").insertAdjacentHTML("beforeend", ne.outerHTML+"<br>");
						document.getElementById("chat_inner_data").scrollTop = document.getElementById("chat_inner_data").scrollHeight;
					});
				});
			}
        </script>
        <? } ?>
    </body>
    <script>
        get_messages_data();
    </script>
    <style>
		html, body {
			background-color: #303030;
			color: #e0e0e0;
			position: fixed;
			top: 0px;
			left: 0px;
			right: 0px;
			bottom: 0px;
			overflow-x: hidden;
			overflow-y: auto;
		}
		h1, h2, p, a, .text {
			color: #e0e0e0;
		}
		.centriert {
			display: flex;
			justify-content: center;
			align-items: center;
			text-align: center;
		}
		.chatgruppe {
			max-width: calc( 100% - 40px );
			width: 500px;
			
			height: auto;
			margin-top: 10px;
			margin-left: 20px;
			border-bottom: 1px solid black;
			border-top: 1px solid black;
			cursor: pointer;
			background-color: darkslategray;
			padding-left: 10px; 
			border-radius: 5px;
			padding-top: 5px;
			padding-bottom: 5px;
		}
		.schueler_container {
			margin: 20px;
			height: auto;
			width: 150px;
			max-width: calc( 100% - 40px );
			border: 1px solid black;
			border-radius: 20px;
			float: left;
		}
		.blog_entry {
			margin: 20px;
			min-height: 300px;
			height: auto;
			max-height: 800px;
			width: 500px;
			max-width: calc( 100% - 40px );
			border: 1px solid black;
			border-radius: 20px;
			float: left;
		}
		button {
		    cursor: pointer;	
		}
	</style>
</html>
