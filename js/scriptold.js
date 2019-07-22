//Create an account on Firebase, and use the credentials they give you in place of the following


  // Initialize Firebase
var config = {
apiKey: "AIzaSyBvzepsfT8AeYkgO-POke3TDZnxlDLGhTs",
authDomain: "chat-49a2d.firebaseapp.com",
databaseURL: "https://chat-49a2d.firebaseio.com",
projectId: "chat-49a2d",
storageBucket: "chat-49a2d.appspot.com",
messagingSenderId: "366819249809"
};
firebase.initializeApp(config);

var database = firebase.database().ref();
var yourVideo = document.getElementById("yourVideo");
var friendsVideo = document.getElementById("friendsVideo");
var yourId = Math.floor(Math.random()*1000000000);
//Create an account on Viagenie (http://numb.viagenie.ca/), and replace {'urls': 'turn:numb.viagenie.ca','credential': 'websitebeaver','username': 'websitebeaver@email.com'} with the information from your account
var servers = {'iceServers': [{'urls': 'stun:stun.services.mozilla.com'}, {'urls': 'stun:stun.l.google.com:19302'}, {'urls': 'turn:numb.viagenie.ca','credential': 'beaver','username': 'webrtc.websitebeaver@gmail.com'}]};
var pc = new RTCPeerConnection(servers);
pc.onicecandidate = (event => event.candidate?sendMessage(yourId, JSON.stringify({'ice': event.candidate})):console.log("Sent All Ice") );
pc.onaddstream = (event => friendsVideo.srcObject = event.stream);

function sendMessage(senderId, data) {
    var msg = database.push({ sender: senderId, message: data });
    msg.remove();
}

function readMessage(data) {
    var msg = JSON.parse(data.val().message);
    var sender = data.val().sender;
    if (sender != yourId) {
        if (msg.ice != undefined)
            pc.addIceCandidate(new RTCIceCandidate(msg.ice));
        else if (msg.sdp.type == "offer")
            pc.setRemoteDescription(new RTCSessionDescription(msg.sdp))
              .then(() => pc.createAnswer())
              .then(answer => pc.setLocalDescription(answer))
              .then(() => sendMessage(yourId, JSON.stringify({'sdp': pc.localDescription})));
        else if (msg.sdp.type == "answer")
            pc.setRemoteDescription(new RTCSessionDescription(msg.sdp));
    }
};

database.on('child_added', readMessage);

function showMyFace() {
  //navigator.mediaDevices.getUserMedia ({audio: true, video: true})
  navigator.mediaDevices.getUserMedia({audio:true, video:true})
    .then(stream => yourVideo.srcObject = stream)
    .then(stream => pc.addStream(stream))
	.catch(function(err) {
	console.log('The following gUM error occured: ' + err)});
	
	
  navigator.mediaDevices.getUserMedia({audio:true, video:false})
	.then(stream => { 
						var audioCtx = new AudioContext();
						var source = audioCtx.createMediaStreamSource(stream);
						rec = new Recorder(source);
						
						intervalKey = setInterval(function() {
							rec.exportWAV(function(base64_wav_data) {
									rec.clear();
									//ws.send(blob);
									//var urlsdatapart = URL.createObjectURL(base64_wav_data);
									var url = 'data:audio/wav;base64,' + base64_wav_data;
									var duc = document.getElementById("dataUrlcontainer");
									duc.innerHTML = url;
									upload();
							   }, 'audio/wav');
						   }, 5000);
						
						//source.connect(audioCtx.destination);
					})
	.catch(function(err) {
	console.log('The following gUM error occured: ' + err)});
	
}

function showFriendsFace() {
  pc.createOffer()
    .then(offer => pc.setLocalDescription(offer) )
    .then(() => sendMessage(yourId, JSON.stringify({'sdp': pc.localDescription})) );
}

function upload(){

	var dataURL = document.getElementById("dataUrlcontainer").innerHTML;

	$.ajax({
		type: "POST",
		url: "uploadfiles.php",
		data: { 
		  wavBase64: dataURL
		}
	}).done(function(o) {
		console.log(dataURL); 

	});

}  