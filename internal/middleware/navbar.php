<header class="navbar">
  <a href="#" class="logo">MEG-Chat</a>
  <div class="menu-icon">&#9776;</div>
  <nav class="navigation">
    <ul>
      <li><a href="javascript:page_navigate('/');">Startseite</a></li>
      <li><a href="javascript:page_navigate('/schueler/');">Schülerliste</a></li>
      <li><a href="javascript:page_navigate('/chat/list');">Chats</a></li>
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
		<button onclick="page_navigate('/account/login');" class="login-button"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19.5 12h-9.8l3.9-3.9c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0l-5.7 5.7c-.4.4-.4 1 0 1.4l5.7 5.7c.4.4 1 .4 1.4 0s.4-1 0-1.4L9.7 13h9.8c.6 0 1-.4 1-1s-.4-1-1-1z"/></svg></button>
        <button onclick="page_navigate('/account/register');" class="register-button"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 5v14H4V5h16m0-2H4C2.9 3 2 3.9 2 5v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/><path d="M12 14.2c-1.4 0-2.6-1.2-2.6-2.6S10.6 9 12 9s2.6 1.2 2.6 2.6-1.2 2.6-2.6 2.6zm0-4.6c-.6 0-1.1.5-1.1 1.1s.5 1.1 1.1 1.1 1.1-.5 1.1-1.1-.5-1.1-1.1-1.1zM18.4 14.2c-.4 0-.7-.3-.7-.7 0-.4.3-.7.7-.7s.7.3.7.7c0 .4-.3.7-.7.7zm0-1.3c-.2 0-.3.2-.3.3 0 .2.2.3.3.3.2 0 .3-.2.3-.3 0-.2-.2-.3-.3-.3z"/></svg></button>
	<?php } ?>
  </div>
</header>
