<header class="navbar">
  <a href="#" class="logo">MEG-Chat</a>
  <div class="menu-icon">&#9776;</div>
  <nav class="navigation">
    <ul>
      <li><a href="javascript:page_navigate('/');">Startseite</a></li>
      <li><a href="javascript:page_navigate('/schueler/');">Schülerliste</a></li>
      <li><a href="javascript:page_navigate('/chat/list');">Chats</a></li>
      <li><a href="javascript:page_navigate('/gallerie');">Gallerie</a></li>
      <li><a href="javascript:page_navigate('/lotto/');">Lotto</a></li>
    </ul>
  </nav>
  <div class="user-profile">
	<?php if(isset($_SESSION['pupil'])){ ?>
    <img src="<?php echo htmlspecialchars(empty($pupil_data['avatar']) ? "/resources/images/avatar.png" : $pupil_data['avatar']); ?>" alt="Profilbild">
    <div class="user-info">
      <span class="user-name"><?php echo htmlspecialchars($pupil_data['fullname']); ?></span>
      <span class="user-balance"><?php echo htmlspecialchars(isset($pupil_data['money']) ? $pupil_data['fullname'] : 0); ?> MEG-Taler</span>
    </div>
    <a href="javascript:page_navigate('/schueler/<?php echo htmlspecialchars($pupil_data['id']); ?>');" class="settings-icon">&#9881;</a>
    <?php } else { ?>
		<button onclick="page_navigate('/account/login');" class="login-button">Anmelden</button>
        <button style="margin-left: 8px; " onclick="page_navigate('/account/register');" class="register-button">Regestrieren</button>
	<?php } ?>
  </div>
</header>