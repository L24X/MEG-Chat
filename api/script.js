window.listeners = {};
window.lasts = {};

window.chatserver = {
    onNewMessage: function(then, channel = 0){
		if(!(channel in listeners)) listeners[channel] = [];
	    listeners[channel].push(then);
	},
	send: function(data, channel = 0){
		if(!data) return;
		let post_data = {
		  send: true,
		  channel: channel,
		  data: JSON.stringify(data)
		};
		ajax("https://meg-chat.de/ajax/open/connect.php", post_data);
	}
};

function ajax(file, params = {}, callback = false) {
  var url = file + '?';
  var notFirst = false;
  for (var key in params) {
    if (params.hasOwnProperty(key)) {
      url += (notFirst ? '&' : '') + key + "=" + params[key];
    }
    notFirst = true;
  }
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      if(callback) callback(xmlhttp.responseText);
    }
  };
  xmlhttp.open('GET', url, true);
  xmlhttp.send();
}

function load_channel(channel){
	return new Promise(function(resolve, reject){
		let data = {
		  channel: channel,
		  last: lasts[channel] || -1
		};
		
		ajax("https://meg-chat.de/ajax/open/connect.php", data, function(responseText){
			var data = JSON.parse(responseText);
			data.forEach(function(m){
				lasts[channel] = m.id;
				try {
				    listeners[channel].forEach(function(l){
						try {
					        l(m.data);
					    } catch(e){
						    console.log(e);	
						}
					});
				} catch(e){
				    console.log(e);	
				}
			});
		    
		    resolve();
		});
	});
}
async function load(){
	for(var i = 0; i < Object.keys(listeners).length; i++){
		var channel = Object.keys(listeners)[i];
	    await load_channel(channel)
	};
	setTimeout(load, 100);
}
load();


