if(self != top) { 
  document.querySelector("html").innerHTML = "";
  top.location = self.location;
}
window.runned_onload = false;
window.addLoadEvent = function(func) {
  if(runned_onload){
	  return func();
  }
  var oldonload = window.onload; 
  if (typeof window.onload != 'function') { 
    window.onload = func;
  } else {
    window.onload = function() { 
      if (oldonload) { 
        oldonload(); 
      } 
      func(); 
    }
  }
}
function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}
addLoadEvent(function(){
	runned_onload = true;
    if ("serviceWorker" in navigator) {
	    navigator.serviceWorker.register("/sw.js").then((registration) => {
	        console.log("[ServiceWorker**] - Registered");
	        window.registration = registration;
	        return registration.pushManager.getSubscription().then(async (subscription) => {
                console.log("[ServiceWorker**] - Push Manager Aktivated");
                if (subscription) return subscription;
			    const response = await fetch("./vapidPublicKey");
				const vapidPublicKey = await response.text();
				const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);
				
				registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertedVapidKey
                });
            });
	    }).then((subscription) => {
            if(!subscription) return;
	        fetch("/internal/logic/push_register.php", {
			  method: "POST",
			  headers: {
			    "Content-type": "application/json",
			  },
			  body: JSON.stringify({ subscription }),
			});
	    });
	}
	window.addEventListener("popstate", function(){
		page_navigate(window.location.href);
	}, false);
});

window.playCustumSound = function(url){
	var audio = new Audio(url);
	audio.volume = 0.35;
	audio.play();
}
window.sleep = function(milliseconds) {
	return new Promise(resolve => setTimeout(resolve, milliseconds));
}
window.browserNotification = function(title, content, url) {
    if (!window.Notification) {
        console.log('Browser does not support notifications.');
    } else {
        if (Notification.permission === 'granted') {
            var notify = new Notification(title, {
                body: content,
                icon: '/resources/images/logo.png',
            });
            if(url != undefined){
                notify.onclick = (e) => {
                    window.parent.parent.focus();
                }
            }
        } else {
            Notification.requestPermission().then(function (p) {
                if (p === 'granted') {
                    var notify = new Notification(title, {
                        body: content,
                        icon: '/resources/images/logo.png',
                    });
                    if(url != undefined){
                        notify.onclick = (e) => {
                            window.parent.parent.focus();
                        }
                    }
                }
            }).catch(function (err) {
                console.error(err);
            });
        }
    }
};

window.post_request = function(url, data = {}, then = false){
	var postdata = new FormData();
	Object.keys(data).forEach(function(key){
		postdata.append(key, data[key]);
	});
	var xhr = new XMLHttpRequest();
	xhr.open('POST', url, true);
	xhr.onload = function () {
	    if(then){
			try {
		        then(this.responseText);
		    } catch(e){
			    console.log("Verarbeitungsfehler! "+this.responseText);
			}
		}
	};
	xhr.onerror = function () {
	    setTimeout(function(){
			post_request(url, data, then);
		}, 1000);
	};
	xhr.send(postdata);
};

window.spa_url = window.location.pathname;
window.page_navigate_loading = false;
window.page_navigate_queue = {};
window.page_navigate_working_url = false;
window.page_navigate = async function(url, from, to, loading_message = true) {
	if(!url) return;
	if(page_navigate_loading){
		page_navigate_queue[url] = {from: from, to: to, loading_message: loading_message};
		if(Object.keys(page_navigate_queue).length > 3){
			delete page_navigate_queue[Object.keys(page_navigate_queue)[0]];
			console.log("[WARNING] Running 3 Reqeusts behind!");
	    }
	    return;
	}
	page_navigate_loading = true;
	if(url in page_navigate_queue) delete page_navigate_queue[url];
	
	var to_text = to;
	if(!url) {
	    url = window.location.pathname;
	}
    if(!from) from = "#site_container";
    if(to && to.split) to=document.querySelector(to);
    if(!to) to=document.querySelector(from);
    
    if(!to){
		from = "body";
		to=document.querySelector(from);
	}
    
    var fertig = false;
    page_navigate_working_url = url;
	
	setTimeout(function(){
	    if(!fertig && loading_message) to.innerHTML = "<h2 style='text-align: center; margin-top: 80px; ' class='text'>Wird geladen..</h2>";
	}, 50);
	
    var XHRt = new XMLHttpRequest();
    XHRt.onload = async function() {
		fertig = true;
		page_navigate_loading = false;
		
		if(Object.keys(page_navigate_queue).length > 0){
			setTimeout(function(){
				if(Object.keys(page_navigate_queue).length == 0) return;
				var url = Object.keys(page_navigate_queue)[Object.keys(page_navigate_queue).length-1];
				var data = page_navigate_queue[url];
				page_navigate(url, data.from, data.to, data.loading_message);
			}, 20);
		}
		
		var parser = new DOMParser();
        var doc = parser.parseFromString(XHRt.responseText, "text/html");
		to.innerHTML = doc.querySelector(from).innerHTML;
		
		if(document.querySelector("title") && doc.querySelector("title")){
		    document.querySelector("title").innerText = doc.querySelector("title").innerText;
		}
		
		if(loading_message) {
			spa_url = page_navigate_working_url;
			window.history.pushState({}, "", page_navigate_working_url);
		}
		
		if(!document.getElementById("right_top_user") != !doc.getElementById("right_top_user")){
		    delete_cache();
		    window.location.reload();
		}
		
		//Only fpr MEG-Chat Lotto App:
		if(spa_url.startsWith("/lotto/")) updateJackpot();
		
		//Only for MEG-Chat App:
		if(spa_url.startsWith("/chat/") && document.getElementById("chat_container")) setTimeout(get_messages_data, 50);
	};
	XHRt.onerror = function() {
		fertig = true;
		to.innerHTML = "<h2 style='text-align: center; margin-top: 80px; color: red; '>Ladefehler!</h2>";
		page_navigate_loading = false;
		page_navigate(page_navigate_working_url, from, to_text, loading_message);
		if(Object.keys(page_navigate_queue).length > 0){
			var url = Object.keys(page_navigate_queue)[Object.keys(page_navigate_queue).length-1];
			var data = page_navigate_queue[url];
			page_navigate(url, data.from, data.to, data.loading_message);
		}
	};
    XHRt.open("GET", url, true);
    XHRt.send(); 
    return XHRt;
}

window.html_popup = function(header, html, can_close = true, bgcolor = "white", color = "black"){
	popup(header, html, can_close, bgcolor, color, true);
};
window.popup = function(header, text, can_close = true, bgcolor = "white", color = "black", html = false){
	var e = document.createElement("div");
	e.classList.add("popup", "no_scrollbar");
	e.style = "position: fixed; top: 0px; right: 0px; bottom: 0px; left: 0px; display: flex; justify-content: center; align-items: center; overflow-x: hidden; ";
	var a = document.createElement("div");
	a.style = "width: 500px; max-width: 99%; height: auto; max-height: 90%; min-height: 100px; border-radius: 20px; box-shadow: 8px 8px 46px -5px rgba(0,0,0,0.62); -webkit-box-shadow: 8px 8px 46px -5px rgba(0,0,0,0.62); -moz-box-shadow: 8px 8px 46px -5px rgba(0,0,0,0.62); position: relative; overflow-x: hidden; overflow-y: auto;";
	a.style.color = color;
	a.style.backgroundColor = bgcolor;
	a.onclick = function(event){
		event.preventDefault();
        event.stopPropagation();
	}
	if(can_close){
		function onclose(){
			e.style.display = "none";
			try {
			    e.remove();
			} catch(e){
			    console.log(e);	
			}
			delete e;
		}
		var b = document.createElement("div");
		b.style = "position: absolute; top: 0px; right: 0px; height: 40px; width: 40px; display: flex; justify-content: center; align-items: center; font-size: 30px; cursor: pointer;";
		b.innerHTML = "&#10006;";
		b.onclick = onclose;
	    a.appendChild(b);
	    e.onclick = onclose;
	}
	var c = document.createElement("div");
	c.style = "width: calc( 100% - 70px ); margin-left: 20px; margin-top: 10px; ";
	var d = document.createElement("h2");
	d.innerText = header;
	d.style.color = color;
	c.appendChild(d);
	var f = document.createElement("p");
	f.style = "color: black; font-size: 14px;";
	if(html){
	    f.innerHTML = text;
	} else {
		f.innerText = text;
	}
	c.appendChild(f);
	a.appendChild(c);
	e.appendChild(a);
	document.body.appendChild(e);
};
window.close_all_popups = function(){
    [...document.getElementsByClassName("popup")].forEach(function(e){
		e.style.display = "none";
	    e.remove();	
	});
};

window.vote = function(id){
	post_request("/ajax/vote.php", {vote: id}, function(data){
		if(data.length > 2){
		    popup("Fehler!", data);
		} else {
		    page_navigate(window.location.href, ".schueler_vote_count_"+id, ".schueler_vote_count_"+id, false);
		    var c = Number(document.getElementsByClassName("schueler_vote_count_"+id)[0].innerText);
			c++;
			[...document.getElementsByClassName("schueler_vote_count_"+id)].forEach(function(e){
			    e.innerText = c;	
			});
		}
	});
};

window.openTab = function(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}

setInterval(function(){
	if(document.getElementById("all_container")){
		page_navigate("/chat/list", "#all_container", "#all_container", false);
	}
}, 2000);

window.get_notification_permission = function(){
	close_all_popups();
	Notification.requestPermission().then((result) => {
        if (result === "granted") {
            randomNotification();
        }
    });
};

window.never_ask_for_notifications = function(){
	close_all_popups();
	localStorage.setItem('noNotifications', true);
};

window.ask_for_notification_permissions = function(){
	if(!Notification) return;
	if (Notification.permission !== "granted" && !localStorage.getItem('noNotifications')) {
		html_popup("Benachrichtigungen für dieses Gerät aktivieren", '<p style="font-size: 16px; ">Der MEG-Chat braucht die Berechtigung Ihnen neue Nachrichten direkt anzuzeigen. Bitte aktivieren Sie diese Funktion wenn Sie immmer auf dem neuesten Stand bleiben wollen.</p><button onclick="never_ask_for_notifications();">Auf diesem Gerät nicht mehr Fragen</button><button onclick="get_notification_permission();">Benachrichtige mich</button>');
    }
};

window.edit_about_me = function(){
	html_popup("Erzähl etwas über dich..", '<textarea style="width: 100%; height: 200px; resize: none; " id="about_me_editor"></textarea><button onclick="save_about_me();">Speichern</button>');
};
window.save_about_me = function(){
	var value = document.getElementById("about_me_editor").value;
	post_request("/ajax/profile_edit.php", {key: "about_me", value: value}, function(data){
		if(data.length > 2){
		    popup("Fehler!", data);
		} else {
			close_all_popups();
		    page_navigate(window.location.href, "#about_me_text");
		}
	});
	
}
window.edit_email = function(){
	html_popup("Email Adresse bearbeiten", '<input type="email" id="email_editor" placeholder="mustermann.max@meg-bruehl.de"></input><button onclick="save_email();">Speichern</button>');
};
window.save_email = function(){
	var value = document.getElementById("email_editor").value;
	post_request("/ajax/profile_edit.php", {key: "email", value: value}, function(data){
		if(data.length > 2){
		    popup("Fehler!", data);
		} else {
			close_all_popups();
		    page_navigate(window.location.href, "#email_text");
		}
	});
}
window.edit_avatar = function(){
	html_popup("Profilbild ändern", '<input type="text" id="avatar_editor" placeholder="https://example.com/bild.png"></input><button onclick="save_avatar();">Speichern</button>');
};
window.save_avatar = function(){
	var value = document.getElementById("avatar_editor").value;
	post_request("/ajax/profile_edit.php", {key: "avatar", value: value}, function(data){
		if(data.length > 2){
		    popup("Fehler!", data);
		} else {
			close_all_popups();
		    document.getElementById("avatar").src = value;
		}
	});
}
window.upload_avatar = function(){
	var e = document.createElement("input");
	e.type = "file";
	e.accept = "image/*";
	e.onchange = async function(){
		var file = e.files[0];
		if(!file) return;
	    var dataUrl = await new Promise(resolve => {
	      let reader = new FileReader();
	      reader.onload = () => resolve(reader.result);
	      reader.readAsDataURL(file);
	    });
	    post_request("/ajax/profile_edit.php", {key: "avatar", value: dataUrl}, function(data){
		if(data.length > 2){
		    popup("Fehler!", data);
		} else {
			close_all_popups();
		    document.getElementById("avatar").src = dataUrl;
		}
	});
	};
	e.click();
};

function addLottoTicket(id, numbers, status) {
	if(!document.getElementById("lotto_ticket_"+id)){
	    const ticketsContainer = document.getElementById("tickets-container");
	
	    const ticketElement = document.createElement("div");
	    ticketElement.id = "lotto_ticket_"+id;
	    ticketElement.classList.add("lotto-ticket");
	
	    const numbersContainer = document.createElement("div");
	    numbersContainer.classList.add("numbers");
	    for (let number of numbers) {
		    const numberElement = document.createElement("div");
		    numberElement.classList.add("number");
		    numberElement.textContent = number;
		    numbersContainer.appendChild(numberElement);
	    }
	    ticketElement.appendChild(numbersContainer);
	    
	    const statusContainer = document.createElement("div");
	    statusContainer.id =  "lotto_ticket_"+id+"_status";
	    statusContainer.style = "width: 100%; height: auto; text-align: center; font-size: 18px; ";
	    ticketElement.appendChild(statusContainer);
	    
	    ticketsContainer.appendChild(ticketElement);
	}
	if(status){
		document.getElementById("lotto_ticket_"+id+"_status").innerText = status.text;
		document.getElementById("lotto_ticket_"+id+"_status").style.color = status.color;
	}
}

window.update_meg_taler_balance = function(){
	page_navigate(window.location.href, ".my_meg_taler_count", ".my_meg_taler_count", false);
}

function startCountdown(endDate) {
  const countdownElement = document.querySelector(".countdown-timer");
   
  if("countdownInterval" in window){
	  clearInterval(window.countdownInterval);
  }
  window.countdownInterval = setInterval(() => {
	  try {
	    const now = new Date().getTime();
	    const distance = endDate.getTime() - now;
	
	    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
	    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
	
	    countdownElement.textContent = `${days.toString().padStart(2, '0')}:${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
	
	    if (distance < 0) {
	        clearInterval(countdownInterval);
	        countdownElement.textContent = "00:00:00:00";
	        startJackpotVibration();
	        showConfetti();
	    }
	  } catch(e){
        console.log(e);  
	  }
  }, 1000);
}

window.updateJackpot = function() {
	post_request("/ajax/jackpot.php", {}, function(data){
		try {
			data = JSON.parse(data);
			var amount = data.balance;
		    const jackpotAmountElement = document.querySelector(".jackpot-amount");
	        jackpotAmountElement.textContent = `${amount} MEG-Taler`;
	        data.tickets.reverse().forEach(function(t){
				addLottoTicket(t.id, Object.values(t.numbers), t.status);
			});
	        startCountdown(new Date(data.draw));
	    } catch(e){
		    console.log(e);	
		}
	});
}
if(window.location.pathname.startsWith("/lotto/")){
	updateJackpot();
	addLoadEvent(updateJackpot);
}

window.lotto_buy_ticket = function(tipp){
	document.getElementById('buy-ticket-popup').style.display = 'none';
	post_request("/ajax/buy_ticket.php", tipp, function(data){
		data = JSON.parse(data);
		if(data.error){
			popup("Fehler!", data.error);
		    return;	
		}
		updateJackpot();
		update_meg_taler_balance();
    });
};
window.start_tipp = function(){
	const numberContainers = document.querySelectorAll('.tipp-number');
	      
	lotto_buy_ticket({
		1: parseInt(numberContainers[0].textContent),
		2: parseInt(numberContainers[1].textContent),
		3: parseInt(numberContainers[2].textContent),
		4: parseInt(numberContainers[3].textContent),
		5: parseInt(numberContainers[4].textContent),
		6: parseInt(numberContainers[5].textContent)
	});
};

window.gallery_upload = async function(){
  var e = document.createElement("input");
  e.type = "file";
  e.accept = "image/*";
  e.onchange = async function(){
	var file = e.files[0];
	if(!file) return;
    var dataUrl = await new Promise(resolve => {
      let reader = new FileReader();
      reader.onload = () => resolve(reader.result);
      reader.readAsDataURL(file);
    });
    document.getElementById("add-image-btn").innerText = "Hochladen...";
    post_request("/ajax/picture_upload.php", {data: dataUrl}, function(data){
		document.getElementById("add-image-btn").innerText = "Neues Bild hinzufügen";
        if(data.length > 2){
	        popup("Fehler!", data);
	    }
	    page_navigate(window.location.href, "#pictures");
	    update_meg_taler_balance();
    });
  };
  e.click();
};

window.last_message_id = -1;
window.loaded_messages_count = 0;
window.chat_id = false;
window.last_message_author_id = false;
window.member_window = false;

window.chat_send_message = function(chatId, text, type = "text"){
    post_request("/ajax/send_message.php", {text: text, chat: chatId, type: type}, function(){
        update_meg_taler_balance();
    });
};

window.message_input_keydown = function(evt) {
	setTimeout(function(){
      document.getElementById("private_message_text").style.height = "auto";
      document.getElementById("private_message_text").style.maxHeight = "250px";
      document.getElementById("private_message_text").style.padding = "0";
      document.getElementById("private_message_text").style.height = document.getElementById("private_message_text").scrollHeight + 'px';
    },0);
	
    evt = evt || window.event;
    var charCode = evt.keyCode || evt.which;
    
    if(!charCode) return;
    
    if(!evt.shiftKey){
		if (charCode == 13) {
			evt.preventDefault();
			
			var value = document.getElementById("private_message_text").value.trim();
			if(value.length == 0) return;
			
			document.getElementById("private_message_text").value = "";
			document.getElementById("private_message_text").rows = 1;
			document.getElementById("private_message_text").style.height = "30px";

			chat_send_message(chat_id, value);
		}
    }
};

function formatFileSize(bytes) {
  const sizes = ['Byte', 'KB', 'MB', 'GB', 'TB', 'EB', 'PB'];
  if (bytes === 0) return '0 Byte';
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
}

window.get_messages_data = async function(){
	if(!document.getElementById("chat_container") || !document.getElementById("chat_inner_data_container")){
	    return;
	}
	function reset_chat(){
		try {
			if(document.getElementById("chat_inner_data")) document.getElementById("chat_inner_data").innerHTML = "";
		    chat_id = false;
			if(spa_url.startsWith("/chat/") && spa_url != "/chat/list"){
				try {
			        chat_id = Number(spa_url.split("/")[spa_url.split("/").length-1]);
			    } catch(e){
				    console.log(e);	
				}
			}
		    last_message_id = -1;
		    loaded_messages_count = 0;
		    last_message_author_id = false;
		} catch(e){
		    console.log(e);	
		}
	}
	if(chat_id != Number(window.location.href.split("/")[window.location.href.split("/").length-1])){
		reset_chat();
	} else if(last_message_id > -1){
		if(!document.getElementById("message_"+chat_id+"_"+last_message_id)){
			reset_chat();
		}
	}
	if(!chat_id) return;
	
	return new Promise(function(resolve, reject){
		function add_to_chat(data){
			var is_first = (last_message_id == -1);
			var first_new = true;
			data.forEach(function(z){
				if(z.chat != chat_id) return;
				
				if(Number(z.id) <= Number(last_message_id) || document.getElementById("message_"+chat_id+"_"+z.id)) return;
				
				if(!document.getElementById("redline")){
				    if(z.new && first_new){
						first_new = false;
						if(is_first || document.hidden){
							var r = document.createElement("div");
							r.style = "width: 100%; height: 1px; background-color: red; margin-top: 10px; ";
							r.id = "redline";
							document.getElementById("chat_inner_data").insertAdjacentHTML("beforeend", r.outerHTML+"<br>");
						}
					}
				} else if(!is_first && !document.hidden){
					document.getElementById("redline").remove();
				}
				
				last_message_id = Number(z.id);
				
				if(last_message_author_id == z.author.id){
					var ne = document.createElement("div");
					ne.style = "width: 100%; height: auto; min-height: 20px; word-warp: break-word; color: white; text-align: left; font-size: 14px; position: relative; word-wrap: break-word; ";
					ne.id = "message_"+chat_id+"_"+z.id;
					var nei = document.createElement("div");
					nei.style = "margin-left: 44px; ";
					var nt = document.createElement("span");
					if(z.type == "text"){
					    nt.innerText = z.text;
					} else if(z.type == "file"){
					    try {
					        var file_data = JSON.parse(z.text);
                            if(file_data.type.startsWith("image")){
                                var fe = document.createElement("img");
                                fe.src = "/files/"+file_data.code;
                                fe.style = "width: auto; height: 200px; max-width: 100%; ";
                                nt.appendChild(fe);
                            } else if(file_data.type.startsWith("audio")) {
                                var fe = document.createElement("audio");
                                fe.src = "/files/"+file_data.code;
                                fe.controls = true;
                                nt.appendChild(fe);
                            } else if(file_data.type.startsWith("video")) {
                                var fe = document.createElement("video");
                                fe.src = "/files/"+file_data.code;
                                fe.controls = true;
                                fe.style = "width: auto; height: 200px; max-width: 100%; ";
                                nt.appendChild(fe);
                            } else {
                                var fe = document.createElement("div");
                                fe.innerText = "Keine Vorschau verfügbar";
                                fe.style = "background-color: black; color: white; display: flex; justify-content: center; align-items: center; width: auto; min-width: 220px; height: 200px; max-width: 100%; ";
                                nt.appendChild(fe);
                            }
                            var fe = document.createElement("a");
                            fe.download = true;
                            fe.href = "/files/"+file_data.code;
                            fe.innerText = "Datei herunterladen ("+formatFileSize(file_data.size)+")";
                            nt.appendChild(fe);
					    } catch(e){
					        nt.innerHTML = '<span style="font-weight: small; font-size: 8px; color: red; ">Konnte nicht geladen werden - Ungültige Daten</span>';
					    }
					} else {
					    nt.innerHTML = '<span style="font-weight: small; font-size: 8px; color: red; ">Konnte nicht geladen werden - Ungültiges Format</span>';
					}

					nt.style = "word-wrap: break-word; ";
					nt.onclick = function(){
					    
					};
					nei.appendChild(nt);
					ne.appendChild(nei);
	
					if(!document.getElementById("chat_inner_data") || !document.getElementById("chat_inner_data_container")) return;
					
					document.getElementById("chat_inner_data").insertAdjacentHTML("beforeend", ne.outerHTML);
					document.getElementById("chat_inner_data_container").scrollTop = document.getElementById("chat_inner_data_container").scrollHeight;
				} else {
					var ne = document.createElement("div");
					ne.style = "width: 100%; height: auto; margin-top: 10px; min-height: 40px; word-warp: break-word; color: white; text-align: left; font-size: 14px; position: relative; transition: all 0.4s; border-radius: 12px; word-wrap: break-word; ";
					ne.id = "message_"+chat_id+"_"+z.id;
					var nei = document.createElement("div");
					nei.style = "margin-left: 44px; margin-top: 4px; ";
					var na = document.createElement("u");
					na.innerText = z.author.username;
					na.style = "font-weight: bold; cursor: pointer; ";
					na.onclick = 'page_navigate("/schueler/'+z.author.id+');';
					nei.appendChild(na);
					var na2 = document.createElement("span");
					na2.innerText = z.time;
					na2.style = "font-size: 8px; font-weight: small; margin-left: 10px; ";
					nei.appendChild(na2);
					var nt = document.createElement("span");
					nt.style = "margin-left: 10px; word-wrap: break-word; ";
					nt.innerText = "\n"+z.text;
					nei.appendChild(nt);
					ne.appendChild(nei);
					var neb = document.createElement("div");
					neb.style = "position: absolute; top: 0px; left: 0px; height: 40px; width: 40px; display: flex; justify-content: center; align-items: center; ";
					var neba = document.createElement("img");
					neba.loading = "lazy";
					neba.style = "width: 34px; height: 34px; border-radius: 50%; ";
					neba.src = z.author.avatar || "/resources/images/avatar.png";
					neb.appendChild(neba);
					ne.appendChild(neb);
					
					if(!document.getElementById("chat_inner_data") || !document.getElementById("chat_inner_data_container")) return;
					
					document.getElementById("chat_inner_data").insertAdjacentHTML("beforeend", ne.outerHTML);
					document.getElementById("chat_inner_data_container").scrollTop = document.getElementById("chat_inner_data_container").scrollHeight;
				}
				
				last_message_author_id = z.author.id;
				loaded_messages_count++;
				
				var messages_count = Number(document.getElementById("chat_messages_count").innerText);
				if(loaded_messages_count > messages_count){
				    messages_count++;
				    document.getElementById("chat_messages_count").innerText = messages_count;
				}
				
				try {
					if(!("saved" in z)){
						z.new = false;
						z.saved = true;
						var o = JSON.parse(localStorage.getItem("chat_"+chat_id) || "[]");
						o.push(z);
						if(o.length > 255) delete o[0];
						localStorage.setItem("chat_"+chat_id, JSON.stringify(o));
					}
				} catch(e){
				    console.log(e);	
				}
			});
		}
		try {
			var has = [];
			var o = JSON.parse(localStorage.getItem("chat_"+chat_id) || "[]");
			o.forEach(function(z){
			    if(z.id > last_message_id) has.push(z);	
			});
			if(has.length > 0){
				add_to_chat(has);
			}
		} catch(e){
		    console.log(e);	
		}
		if(!("running_chat_reader" in window)) {
			window.running_chat_reader = false;
		}
		if(running_chat_reader) return;
		window.running_chat_reader = true;
		post_request("/ajax/load_new_messages.php", {chat: chat_id, last: Number(last_message_id)}, function(data){
			window.running_chat_reader = false;
			setTimeout(async function(){
			    get_messages_data();
			}, 200);
			
			data = JSON.parse(data);
			while(data.length > 255) delete data[0];
			add_to_chat(data);
			
			resolve();
		});
    });
}
get_messages_data();
addLoadEvent(get_messages_data);

window.jump_to_message = function(message_id){
	if(!document.getElementById("message_"+chat_id+"_"+message_id)) return;
	document.getElementById("message_"+chat_id+"_"+message_id).scrollIntoView();
	document.getElementById("message_"+chat_id+"_"+message_id).style.backgroundColor = "lightgray";
	setTimeout(function(){
		document.getElementById("message_"+chat_id+"_"+message_id).style.backgroundColor = "transparent";
	}, 200);
};
window.chat_members_info = function(){
	page_navigate("/chat/"+chat_id+"?members=true", "#chat_inner_data_content_container");
	member_window = true;
};
window.chat_messages_info = function(){
	page_navigate("/chat/"+chat_id, "#chat_inner_data_content_container");
	member_window = false;
};

addLoadEvent(function(){
	const numberContainers = document.querySelectorAll('.tipp-number');

	document.addEventListener('wheel', (event) => {
	  if (event.target.closest('.tipp-numbers-container')) {
	    const direction = event.deltaY > 0 ? 1 : -1;
	    
	    const index = Array.from(numberContainers).indexOf(event.target);
	  
	    if (index !== -1) {
	      const currentNumber = parseInt(numberContainers[index].textContent);
	      
	      const newNumber = (currentNumber + direction + 100) % 100;
	      
	      numberContainers[index].textContent = newNumber;
	    }
	  }
	});
});

window.startJackpotVibration = function() {
  const jackpotBox = document.querySelector('.jackpot-box');
  let intensity = 0;
  let duration = 100;
  const maxIntensity = 40;
  const maxDuration = 5000;

  const interval = setInterval(() => {
    if (intensity >= maxIntensity) {
      clearInterval(interval);
      return;
    }

    const randomX = Math.random() * intensity - intensity / 2;
    const randomY = Math.random() * intensity - intensity / 2;
    jackpotBox.style.transform = `translate(${randomX}px, ${randomY}px)`;

    intensity += 2;
    duration -= 1;

    if (duration <= 0) {
      duration = maxDuration;
      intensity -= 1;
    }
  }, 50);
}

window.showConfetti = function(duration = 10) {
  const confettiContainer = document.createElement('div');
  confettiContainer.style.position = 'fixed';
  confettiContainer.style.top = '0';
  confettiContainer.style.left = '0';
    confettiContainer.style.bottom = '0';
  confettiContainer.style.right = '0';
  confettiContainer.style.zIndex = '9999';
  confettiContainer.style.mouseEvents = 'none';
  document.body.appendChild(confettiContainer);

  const colors = ['#f6c667', '#ef6eae', '#53b3cb', '#8bc34a', '#e65100', '#e91e63', '#4caf50'];
  const shapes = ['circle', 'square', 'triangle', 'heart'];
  const animations = ['a', 'b', 'c', 'd', 'e'];
  const numConfettis = 200;
  const minSize = 10;
  const maxSize = 30;
  const minSpeed = 8;
  const maxSpeed = 80;

  const confettiElements = [];

  for (let i = 0; i < numConfettis; i++) {
    const confetti = document.createElement('div');
    const size = Math.floor(Math.random() * (maxSize - minSize + 1)) + minSize;
    const color = colors[Math.floor(Math.random() * colors.length)];
    const shape = shapes[Math.floor(Math.random() * shapes.length)];
    const animation = animations[Math.floor(Math.random() * animations.length)];
    const speed = Math.floor(Math.random() * (maxSpeed - minSpeed + 1)) + minSpeed;

    confetti.style.width = `${size}px`;
    confetti.style.height = `${size}px`;
    confetti.style.borderRadius = shape === 'circle' ? '50%' : '0';
    confetti.style.backgroundColor = color;
    confetti.style.position = 'fixed';
    confetti.style.top = '-'+maxSize+'px';
    confetti.style.left = `${Math.random() * 100}%`;
    confetti.style.animation = `confetti-${animation} ${speed}s linear forwards`;

    confettiElements.push(confetti);
    confettiContainer.appendChild(confetti);
  }
  
  var count = 0;
  var spammer = setInterval(function(){
	  count++;
	  if(count >= duration){
		  clearInterval(spammer);
	      return;  
	  }
	  showConfetti(duration-count);
  }, 500);
  setTimeout(() => {
    confettiElements.forEach(confetti => confetti.remove());
    confettiContainer.remove();
  }, maxSpeed*1000);
}

window.delete_cache = function(){
	if("registration" in window){
		registration.active.postMessage({action: "clear"});
	}
}

window.uploadFile = function(file, progressHandler, completeHandler) {
  var chunkSize = 2 * 1024 * 1024;
  var fileSize = file.size;
  var offset = 0;
  var fileid = Math.round(Math.random()*1000000);

  function uploadChunk() {
    var chunk_data = file.slice(offset, offset + chunkSize);
    var chunk = new File([chunk_data], fileid+"_"+file.name, { type: file.type })
    
    var formData = new FormData();
    formData.append('file', chunk);
    formData.append('offset', offset);
    formData.append('filesize', fileSize);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/ajax/upload_file.php', true);

    xhr.upload.addEventListener('progress', function(e) {
      if (e.lengthComputable) {
        var percentComplete = ((offset+e.loaded) / fileSize) * 100;
        progressHandler(percentComplete);
      }
    }, false);

    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          var data = JSON.parse(xhr.responseText);
          if(data.status == "uploading"){
              offset += chunkSize;
              uploadChunk();
          } else if(data.status == "complete"){
              completeHandler(data.code);
          } else if(data.status == "position"){
              offset = data.offset;
              uploadChunk();
          } else if(data.status == "error"){
              popup("Fehler!", data.message);
          }
        } else {
          console.error(xhr.statusText);
          uploadChunk();
        }
      }
    };
    xhr.onerror = function(){
        uploadChunk();
    }

    xhr.send(formData);
  }

  uploadChunk();
}
function chooseFile() {
  return new Promise((resolve, reject) => {
    var input = document.createElement('input');
    input.type = 'file';
    input.addEventListener('change', () => {
      if (input.files && input.files[0]) {
        resolve(input.files[0]);
      } else {
        resolve(false);
      }
    });
    input.click();
  });
}
window.chatUploadFile = async function(){
	var file = await chooseFile();
	if(!file) return;
	var chatId = chat_id;
	var e = document.createElement("div");
	e.style = "width: 100%; height: 50px; color: white; text-align: right; padding-right: 15px; ";
	var t = document.createElement("h4");
	t.innerText = file.name+" wird hochgeladen...";
	e.appendChild(t);
	var i = document.createElement("span");
	i.innerText = "Vorbereiten..";
	e.appendChild(i);
    document.getElementById("sub_navbar").appendChild(e);
	uploadFile(file, function(p){
		i.innerText = p.toFixed(2)+"% abgeschlossen..";
	}, function(code){
	    i.innerText = "Abschließen..";
	    e.remove();
        console.log(code, chatId);
        setTimeout(function(){
            console.log(code, chatId);
            chat_send_message(chatId, JSON.stringify({name: file.name, code: code, size: file.size, type: file.type}), "file");
        }, 20);
        console.log(code, chatId);
	});
}
