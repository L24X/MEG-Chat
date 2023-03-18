window.listeners = {};
window.lasts = {};

window.chatserver = {
    onNewMessage: function(then, channel = 0){
		if(!(channel in listeners)) listeners[channel] = [];
	    listeners[channel].push(then);
	},
	send: function(data, channel = 0){
		if(!data) return;
		
		
	}
};

function load_channel(channel){
	return new Promise(function(resolve, reject){
		let xhr = new XMLHttpRequest();
		xhr.open("POST", "https://meg-chat.de/ajax/open/connect.php");
		xhr.setRequestHeader("Accept", "application/json");
		xhr.setRequestHeader("Content-Type", "application/json");
		
		xhr.onreadystatechange = function () {
			resolve();
		    if (xhr.readyState === 4) {
				var data = JSON.parse(xhr.responseText);
				listeners[channel].forEach(function(l){
					try {
					    data.forEach(function(m){
							try {
						        l(m);
						    } catch(e){
							    console.log(e);	
							}
						});
					} catch(e){
					    console.log(e);	
					}
				});
				
		    }
		};
		
		let data = {
		  channel: channel,
		  last: lasts[channel] || -1;
		};
		
		xhr.send(JSON.stringify(data));
	});
}
async function load(){
	for(var i = 0; i < Object.keys(listeners).length; i++){
		var channel = Object.keys(listeners)[i];
	    await load_channel(channel)
	});
	setTimeout(load, 100);
}
load();
