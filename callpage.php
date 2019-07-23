<?php

$error = false;
if (isSet($_GET['roomID']))
{
	$roomID 		= $_GET['roomID'];
}
else
{
	$error = true;
}

if (isSet($_GET['userID']))
{
	$userID 		= $_GET['userID'];
}
else
{
	$error = true;
}

if (isSet($_GET['username']))
{
	$username 		= $_GET['username'];
}
else
{
	$error = true;
}

if (isSet($_GET['usertype']))
{
	$usertype 		= $_GET['usertype'];
}
else
{
	//$error = true;
}

if (isSet($_GET['token']))
{
	$token 		= $_GET['token'];
}
else
{
	$error = true;
}

if ($error == true)
{
	echo "NOT ALLOWED";
}
else
{

?>


<html>
  <head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
  </head>
  <body onload="showMyFace()">
    <video id="yourVideo" autoplay muted playsinline></video>
    <video id="friendsVideo" autoplay playsinline></video>
    <br />
    <button onclick="showFriendsFace()" type="button" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-facetime-video" aria-hidden="true"></span> Call</button>
	<button onclick="stopSendingData()" id="stop" class="btn btn-primary btn-lg">Stop</button>
	<div id="dataUrlcontainer" hidden></div>

	<script src="https://www.gstatic.com/firebasejs/5.9.3/firebase.js"></script>
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<!--<script src="js/recorder.js"></script>-->
    <script>
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
//var yourId = Math.floor(Math.random()*1000000000);
var userId = <?php echo $userID;?>;
var roomId = <?php echo $roomID;?>;
//Create an account on Viagenie (http://numb.viagenie.ca/), and replace {'urls': 'turn:numb.viagenie.ca','credential': 'websitebeaver','username': 'websitebeaver@email.com'} with the information from your account
var servers = {'iceServers': [{'urls': 'stun:stun4.l.google.com:19302'}]};
//var servers = {'iceServers': [{'urls': 'stun:stun.services.mozilla.com'}, {'urls': 'stun:stun.l.google.com:19302'}, {'urls': 'turn:numb.viagenie.ca','credential': 'z1234567','username': 'markomilicmaki@gmail.com'}]};
var pc = new RTCPeerConnection(servers);
pc.onicecandidate = (event => event.candidate?sendMessage(userId, roomId, JSON.stringify({'ice': event.candidate})):console.log("Sent All Ice") );
pc.onaddstream = (event => friendsVideo.srcObject = event.stream);

function sendMessage(senderId, froomId, data) {
    var msg = database.push({ sender: senderId, room: froomId, message: data });
	console.log('Sent message: ' + msg);
    msg.remove();
}

function readMessage(data) {
    var msg = JSON.parse(data.val().message);
	console.log('Received message: ' + msg);
    var sender = data.val().sender;
    if (sender != userId) {
        if (msg.ice != undefined)
            pc.addIceCandidate(new RTCIceCandidate(msg.ice));
        else if (msg.sdp.type == "offer")
            pc.setRemoteDescription(new RTCSessionDescription(msg.sdp))
              .then(() => pc.createAnswer())
              .then(answer => pc.setLocalDescription(answer))
              .then(() => sendMessage(userId, roomId, JSON.stringify({'sdp': pc.localDescription})));
        else if (msg.sdp.type == "answer")
            pc.setRemoteDescription(new RTCSessionDescription(msg.sdp));
    }
};

database.on('child_added', readMessage);

var chunks = [];
var mediaRecorder;
var intervalKey;
var chunksOff = [];
var chunksOn = [];
var phaseOn = true;

function getTimestamp()
{
  var d = new Date();
  var d1 = Date.UTC(d.getFullYear(), d.getMonth(), d.getDay(), d.getHours(), d.getMinutes(), d.getSeconds(), d.getMilliseconds() );
  return d1;
}

function showMyFace() {
  //navigator.mediaDevices.getUserMedia ({audio: true, video: true})
  navigator.mediaDevices.getUserMedia({audio:true, video:true})
    .then(stream => yourVideo.srcObject = stream)
    .then(stream => pc.addStream(stream))
	.catch(function(err) {
	console.log('The following gUM error occured: ' + err)});
	
	
  navigator.mediaDevices.getUserMedia({audio:true, video:false})
	.then(stream => { 
	
						var options = {
							audioBitsPerSecond : 8196,
							videoBitsPerSecond : 2500000
						}
	
						mediaRecorder = new MediaRecorder(stream, options);
						
						mediaRecorder.ondataavailable = function(evt) {
							if (phaseOn==true)
							{
								chunksOn.push(evt.data);
								//console.log("New chunk ON "+getTimestamp()); 
							}
							else
							{
								chunksOff.push(evt.data);
								//console.log("New chunk OFF "+getTimestamp()); 
							}
						};

						intervalKey = setInterval(function() {
							if (mediaRecorder.state!="inactive")
							{
								mediaRecorder.stop();
								mediaRecorder.start(200);
							}
						}, 5000);
						 
						mediaRecorder.onstop = function(evt) {
							if (phaseOn==true)
							{
								if (chunksOn.length >0)
								{
									// Make blob out of our chunks, and send it.
									var blob = new Blob(chunksOn, { 'type' : 'audio/wav' });
									
									var url = '';
									var reader = new FileReader();
									reader.onload = function(event){
										url = 'data:audio/wav;base64,'+event.target.result;
										var duc = document.getElementById("dataUrlcontainer");
										duc.innerHTML = url;
										upload();
									};
									reader.readAsDataURL(blob);
									
									chunksOn = [];
									//console.log("Sent data chunks on "); 
								}
								phaseOn = false;
								//console.log("Phase change to OFF "+getTimestamp()); 
							} else if (phaseOn==false)
							{
								if (chunksOff.length >0)
								{
									// Make blob out of our chunks, and send it.
									var blob = new Blob(chunksOff, { 'type' : 'audio/wav' });
									
									var url = '';
									var reader = new FileReader();
									reader.onload = function(event){
										url = 'data:audio/wav;base64,'+event.target.result;
										var duc = document.getElementById("dataUrlcontainer");
										duc.innerHTML = url;
										upload();
									};
									reader.readAsDataURL(blob);
									
									chunksOff = [];
									//console.log("Sent data chunks off"); 
								}
								phaseOn = true;
								//console.log("Phase change to ON "+getTimestamp()); 
							}
						};
					})
	.catch(function(err) {
	console.log('The following gUM error occured: ' + err)});
	
}

function showFriendsFace() {
	pc.createOffer()
		.then(offer => pc.setLocalDescription(offer) )
		.then(() => sendMessage(userId, roomId, JSON.stringify({'sdp': pc.localDescription})) );
	mediaRecorder.start(200);
}

function stopSendingData()
{
	yourVideo.pause();
	yourVideo.src="";
	friendsVideo.pause();
	friendsVideo.src="";
	clearInterval(intervalKey);
	mediaRecorder.stop();
	endCall();
}

function endCall()
{
	$.ajax({
		type: "POST",
		<?php if ($usertype=='Agent') { ?>
		url: "endCallAgent.php",
		<?php } else { ?>
		url: "endCallUser.php",
		<?php }  ?>
		data: { 
			username: '<?php echo $username; ?>',
			idcall: <?php echo $roomID; ?>,
			token: '<?php echo $token; ?>'
		}
	}).done(function(o) {
		console.log(o); 
	});
}

function upload(){

	var dataURL = document.getElementById("dataUrlcontainer").innerHTML;

	$.ajax({
		type: "POST",
		url: "performCall.php",
		data: { 
			username: '<?php echo $username; ?>',
			idcall: <?php echo $roomID; ?>,
			token: '<?php echo $token; ?>',
			wavBase64: dataURL
		}
	}).done(function(o) {
		console.log(o); 
	});

}
	</script>
  </body>
</html>

<?php } ?>