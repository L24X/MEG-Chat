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
			    console.log("Netzwerkfehler! "+this.responseText);
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
	    url = window.location.href;
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
		
		//Only for MEG-Chat App:
		if(document.getElementById("chat_container")) setTimeout(get_messages_data, 50);
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

function addLottoTicket(numbers) {
    const ticketsContainer = document.getElementById("tickets-container");

    const ticketElement = document.createElement("div");
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

    ticketsContainer.appendChild(ticketElement);
}

function updateJackpot(amount) {
    const jackpotAmountElement = document.querySelector(".jackpot-amount");
    jackpotAmountElement.textContent = `${amount} MEG-Taler`;
}

window.update_meg_taler_balance = function(){
	page_navigate(window.location.href, ".my_meg_taler_count", ".my_meg_taler_count", false);
}

function startCountdown(endDate) {
  const countdownElement = document.querySelector(".countdown-timer");

  const countdownInterval = setInterval(() => {
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
    }
  }, 1000);
}
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
				post_request("/ajax/send_message.php", {text: value, chat: chat_id, then: update_meg_taler_balance});
			}
			send_chess_message();
		}
    }
};

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
					nt.innerText = z.text;
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
