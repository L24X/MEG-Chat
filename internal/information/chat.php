<?php
require_once("../logic/db.php");

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
}

$chat = isset($_GET['chat']) ? $_GET['chat'] : false;

$chat_data = false;
$member = false;

if($chat){
	$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".chats WHERE id = :id; ");
	$stmtData->execute(array('id' => $chat));
	$row = $stmtData->fetchObject();
	if($row){
		$chat_data = (array)$row;
	}
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

if($chat_data){
	$stmtMemberCount = $db->prepare("SELECT COUNT(id) as count FROM ".DBTBL.".chats_members WHERE chat = :chat; ");
	$stmtMemberCount->execute(array('chat' => $chat_data['id']));
	$member_count = ((array)$stmtMemberCount->fetchObject())['count'];
	
	$stmtMessagesCount = $db->prepare("SELECT COUNT(id) as count FROM ".DBTBL.".chats_messages WHERE chat = :chat; ");
	$stmtMessagesCount->execute(array('chat' => $chat_data['id']));
	$messages_count = ((array)$stmtMessagesCount->fetchObject())['count'];
}

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
        <title>MEG Chat | Chat | <?php echo htmlspecialchars($chat_data ? $chat_data['name'] : "Nicht Gefunden"); ?></title>
        <meta name="description" content="<?php echo htmlspecialchars($chat_data ? $chat_data['description'] : "Dieser Chat exestiert nicht, oder Sie haben keinen Zugriff darauf!"); ?>">
        <meta name="keywords" lang="de" content="meg, max, ernst, gymnasium, max ernst gymnasium, brühl, chat, online, schueler, chatten, austauschen, hausaufgaben, fragen, blog, artikel, austausch, kontakt, neues">
        <?php require('../middleware/head.php'); ?>
    </head>
    <body>
		<?php require('../middleware/navbar.php'); ?>
		<div id="site_container" style="margin-top: 80px; ">
			<?php if(isset($_GET['list'])){ ?>
				<div style="float: left; width: 100%; max-width: 100%; height: auto; max-height: 100%; overflow-x: hidden; overflow-y: auto; " class="no_scrollbar">
		            <div style="width: 100%; height: auto; margin-top: 20px; ">
						<div id="all_container">
		                    <?php require("public_chats.php"); ?>
			            </div>
			        </div>
		        </div>
			<?php } else { ?>
				<?php if(!$is_mobile){ ?>
			        <div style="float: left; width: 540px; max-width: 100%; height: auto; max-height: 100%; overflow-x: hidden; overflow-y: auto; " class="no_scrollbar">
			            <div style="width: 100%; height: auto; margin-top: 20px; ">
							<div id="all_container">
			                    <?php require("public_chats.php"); ?>
				            </div>
				        </div>
			        </div>
			    <?php } ?>
		        <div style="float: left; width: <?php if(!$is_mobile){ ?>calc( 100% - 542px )<?php } else { ?>100%<?php } ?>; min-width: 350px; max-width: 100%; text-align: center; height: 100%; " id="chat_container">
	            <?php if(!$chat_data){ ?>
				    <h1>Entweder dieser Chat exestiert nicht oder zu hast keinen Zugriff darauf. Sollte dieses Problem weiterhin auftauchen melde dich bitte bei einem Administrator.!</h1>
	                <?php
				} else { ?>
					<div style="height: auto; min-height: 110px; margin-top: 6px; " class="centriert">
						<div style="width: auto; height: 100%; " class="centriert">
							<?php if($is_mobile && !isset($_GET['list'])){ ?>
							<div style="height: 110px; width: auto; min-width: 20px; float: left; padding-right: 15px; " class="centriert">
							    <button onclick="if(member_window) { chat_messages_info(); } else { page_navigate('/chat/list'); }" style="min-height: 50px; height: auto; width:  auto; font-size: 16px; background-color: transparent; font-size: 24px; color: white; border: none; outline: none; ">&#8678;</button>
							</div>
							<?php } ?>
							<div onclick="chat_messages_info();" style="height: 110px; width: auto; float: left; " class="centriert">
								<div onclick="chat_messages_info();">
					                <h1 style="margin-top: 0px; font-size: 24px; "><?php echo htmlspecialchars($chat_data['name']); ?></h1>
					                <div style="width: 100%; height: auto; margin-top: 10px; " class="centriert">
					                     <h2 style="margin: 0; padding: 0; font-size: 13px; "><?php echo htmlspecialchars($chat_data['description']); ?></h2>
					                </div>
					            </div>
				            </div>
				            <div style="height: 110px; width: auto; float: left; border-left: 1px solid white; margin-left: 20px; " class="centriert">
				                <div style="margin-left: 20px; text-align: left; ">
				                    <h4 onclick="chat_members_info();" style="text-align: center; cursor: pointer; "><?php echo htmlspecialchars($member_count); ?> Mitglieder</h4>
				                    <h4 onclick="chat_messages_info();" style="margin-top: 10px; text-align: center; cursor: grab; "><span id="chat_messages_count"><?php echo htmlspecialchars($messages_count); ?></span> Nachrichten</h4>
				                </div>
				            </div>
			            </div>
	                </div>
	                <div style="width: 100%; height: calc( 100% - 155px ); min-height: 200px; max-height: 100%; margin-top: 20px; " class="centriert">
	                    <div style="height: 100%; width: 100%; position: relative;" id="chat_inner_data_content_container">
							<?php if(isset($_GET['members'])){ ?>
							<div style="position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; overflow-x: hidden; overflow-y: auto; " class="no_scrollbar">
	                            <div style="width: 100%; height: auto; overflow: hidden; ">
									<div style="width: 100%; height: 50px; position: relative; ">
									    <h2 style="text-align: center; "><u>Mitglieder</u></h2>
									</div>
		                            <?php
							        $stmtMembers = $db->prepare("SELECT ".DBTBL.".pupils.*, COUNT(".DBTBL.".pupils_votes.s_to) AS rating_count, COALESCE(SUM(points),0) as rating FROM ".DBTBL.".pupils LEFT JOIN ".DBTBL.".pupils_votes ON ".DBTBL.".pupils.id = ".DBTBL.".pupils_votes.s_to WHERE pupils.id IN (SELECT pupil FROM ".DBTBL.".chats_members WHERE chat = :chat) GROUP BY ".DBTBL.".pupils.id ORDER BY LOWER(fullname) ASC LIMIT 10000;");
									$stmtMembers->execute(array('chat' => $chat_data['id']));
									while($row = $stmtMembers->fetchObject()){
									    $row = (array)$row;
									    ?>
									    <a href="javascript:page_navigate('/schueler/<?php echo htmlspecialchars($row['id']); ?>');" style="color: black; text-decoration: none; "><div class="schueler_container">
										    <div style="height: calc( 100% - 60px ); width: 100%; margin-top: 10px; " class="centriert">
										        <img loading="lazy" style="width: 130px; height: 130px; border-radius: 50%; " src="<?php echo htmlspecialchars(empty($row['avatar']) ? "/resources/images/avatar.png" : $row['avatar']); ?>">
										    </div>
										    <div style="width: 100%; height: 40px; word-wrap: break-word; " class="centriert">
										        <h3 style="word-wrap: break-word;"><?php echo htmlspecialchars($row['fullname']); ?></h3>
										    </div>
										    <div style="width: 100%; height: 50px; font-size: 14px; overflow-x: hidden; overflow-y: scroll; " class="centriert no_scrollbar">
										        <div style="width: 100%; text-align: center; word-wrap: break-word; " class="centriert">
													<div style="text-align: center; width: auto; height: auto; ">
														<div style="margin-left: 8px; color: black; "><?php echo htmlspecialchars($row['about_me']); ?></div>
													</div>
												</div>
										    </div>
										    <div style="width: 100%; height: 20px; font-size: 14px; " class="centriert">
							                    <div style="width: 100%; text-align: center; " class="centriert">
													<div style="text-align: center; width: auto; height: auto; ">
														<?php if(isset($_SESSION['pupil']) && $pupil_data['activated'] == 1 && $row['id'] != $pupil_data['id']){ ?><a style="color: black; " href="javascript:void(0); " onclick="event.stopPropagation(); vote('<?php echo htmlspecialchars($row['id']); ?>');"><?php } ?>
														<div class="schueler_vote_count_<?php echo htmlspecialchars($row['id']); ?>" style="float: left; color: black; "><?php echo $row['rating']; ?></div>
														<div style="float: left; margin-left: 8px; color: black; ">Stimmen</div>
															<?php if(isset($_SESSION['pupil']) && $pupil_data['activated'] == 1 && $row['id'] != $pupil_data['id']){ ?></a><?php } ?>
													</div>
												</div>
										    </div>
												<?php if(isset($_SESSION['pupil']) && $pupil_data['activated'] == 1 && $row['id'] != $pupil_data['id']){ ?>
											<div style="width: 100%; height: 25px; font-size: 14px; " class="centriert">
											    <button onclick="event.stopPropagation(); vote('<?php echo htmlspecialchars($row['id']); ?>');">Gefällt Mir</button>
											</div>
												<?php }?>
											<div style="width: 100%; height: 10px; "></div>
										</div></a>
									    <?	
									}
							        ?>
	                            </div>
	                            <?php
	                            if($chat_data['public'] == 1){
									?>
									<div style="width: 100%; height: auto; margin-top: 20px; text-align: center; color: red; ">Dieser Chat ist öffentlich. Das heißt das Jeder die Nachrichten in diesem Chat lesen kann, auch ohne Mitglied zu sein.</div>
									<?php
								}
								?>
	                            <div style="width: 100%; height: 25px; "></div>
	                        </div>
							<?php } else { ?>
	                        <div style="position: absolute; top: 0px; left: 0px; right: 0px; bottom: 52px; overflow-x: hidden; overflow-y: auto; " id="chat_inner_data_container" class="no_scrollbar">
	                            <div style="width: 100%; height: auto; overflow: hidden; " id="chat_inner_data"></div>
	                            <div style="width: 100%; height: 25px; "></div>
	                        </div>
	                        <?php
	                        if(!isset($_SESSION['pupil'])){
								?>
								<div style="position: absolute; bottom: 0px; right: 0px; left: 0px; min-height: 50px; height: auto; max-height: 65px; overflow-y: auto; " class="centriert no_scrollbar">
								    <p style="color: red; ">Um selber Nachrichten in diesen Chat schreiben zu können, melde dich bitte an oder regestriere dich. Der Zugang ist nur für Schüler des MEGs erlaubt!</p>
								</div>
	                            <?php
							} else {
							    ?>
							    <textarea rows="1" onkeydown="message_input_keydown(event);" id="private_message_text" style="position: absolute; bottom: 0px; right: 0px; left: 0px; height: 30px;  font-size: 24px; text-align: left; resize: none; background-color: transparent; " class="text" placeholder="Meine Nachricht.."></textarea>
	                            <?php
							}
						    }
							?>
	                    </div>
	                </div>
	            <?php } ?>
		    <?php } ?>
		</div>
    </body>
</html>
