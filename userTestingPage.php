<?php
?>

<html>
<head>
<script src= "http://player.twitch.tv/js/embed/v1.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-animate.js"></script>
<script>
angular.module("myApp", ['ngAnimate'])
  .controller("mainController", function ($scope)
  {
	
	function clearParentPanel()
	{
		var box = document.getElementById('maincontent');
		while (box.firstChild) {
		  box.removeChild(box.firstChild);
		}
	}
	$scope.token = "";
	$scope.userid = "";
	$scope.roomid = "";
	$scope.setToken = function(value) {
		$scope.token = value;
	}
	$scope.setUserid = function(value) {
		$scope.userid = value;
	}
	$scope.setRoomid = function(value) {
		$scope.roomid = value;
	}
	
	function callgetLinkService(FilterParameter)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() 
		{
			var composedtxt = "";
			if (this.readyState == 4 && this.status == 200) 
			{
				var resultscount = 0;
				var composedtxt = "<a href=\"";
				var myObj = JSON.parse(this.responseText);
				composedtxt += myObj.Page;
				composedtxt += "\">LINK</a>";
				var parentElement = document.getElementById("maincontent");
				var newnode = document.createElement("DIV");
				newnode.innerHTML=composedtxt;
				parentElement.appendChild(newnode);
			}
		}		
		
		xmlhttp.open("POST", "getCallLinkService.php", true);
		var sentstring = FilterParameter;
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(sentstring);
	}
	
	function callLoginService(FilterParameter)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() 
		{
			var composedtxt = "";
			if (this.readyState == 4 && this.status == 200) 
			{
				var resultscount = 0;
				var myObj = JSON.parse(this.responseText);
				//for (x in myObj) {
					composedtxt = myObj.Result;
					if (composedtxt=="User logged in")
					{
						var tokenvalue = myObj.Token;
						$scope.setToken(tokenvalue);
						$scope.$apply();
					}
					var parentElement = document.getElementById("maincontent");
					var newnode = document.createElement("DIV");
					newnode.innerHTML=composedtxt;
					parentElement.appendChild(newnode);
				//}
			}
		}		
		
		xmlhttp.open("POST", "loginUser.php", true);
		var sentstring = FilterParameter;
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(sentstring);
	}
	
	function callLogoffService(FilterParameter)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() 
		{
			var composedtxt = "";
			if (this.readyState == 4 && this.status == 200) 
			{
				var resultscount = 0;
				var myObj = JSON.parse(this.responseText);
				//for (x in myObj) {
					composedtxt = myObj.Result;
					var tokenvalue = "";
					$scope.setToken(tokenvalue);
					$scope.$apply();
					var parentElement = document.getElementById("maincontent");
					var newnode = document.createElement("DIV");
					newnode.innerHTML=composedtxt;
					parentElement.appendChild(newnode);
				//}
			}
		}		
		
		xmlhttp.open("POST", "logoffUser.php", true);
		var sentstring = FilterParameter;
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(sentstring);
	}
	
	function callRequestCallService(FilterParameter)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() 
		{
			var composedtxt = "";
			if (this.readyState == 4 && this.status == 200) 
			{
				var resultscount = 0;
				var myObj = JSON.parse(this.responseText);
					composedtxt = myObj.Result;
					if (composedtxt=="Call requested")
					{
						$scope.setRoomid(myObj.CallId);
						$scope.$apply();
					}
					var parentElement = document.getElementById("maincontent");
					var newnode = document.createElement("DIV");
					newnode.innerHTML=composedtxt;
					parentElement.appendChild(newnode);
			}
		}		
		
		xmlhttp.open("POST", "requestCall.php", true);
		var sentstring = FilterParameter;
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(sentstring);
	}
	
	function callCancelCallService(FilterParameter)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() 
		{
			var composedtxt = "";
			if (this.readyState == 4 && this.status == 200) 
			{
				var resultscount = 0;
				var myObj = JSON.parse(this.responseText);
					composedtxt = myObj.Result;
					$scope.setRoomid("");
					$scope.$apply();
					var parentElement = document.getElementById("maincontent");
					var newnode = document.createElement("DIV");
					newnode.innerHTML=composedtxt;
					parentElement.appendChild(newnode);
			}
		}		
		
		xmlhttp.open("POST", "cancelCall.php", true);
		var sentstring = FilterParameter;
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(sentstring);
	}
	
	$scope.lastfilter = "";
	$scope.onGetLinkClick = function () {
		$scope.lastfilter = "username="+$scope.username;
		$scope.lastfilter += "&token="+$scope.token;
		clearParentPanel();
		callgetLinkService($scope.lastfilter);
	}
	
	$scope.onLogin = function () {
		$scope.lastfilter = "username="+$scope.username;
		$scope.lastfilter += "&password="+$scope.password;
		clearParentPanel();
		callLoginService($scope.lastfilter);
	}
	
	$scope.onLogoff = function () {
		$scope.lastfilter = "username="+$scope.username;
		$scope.lastfilter += "&password="+$scope.password;
		clearParentPanel();
		callLogoffService($scope.lastfilter);
	}
	
	$scope.onRequestCall = function () {
		$scope.lastfilter = "username="+$scope.username;
		$scope.lastfilter += "&token="+$scope.token;
		clearParentPanel();
		callRequestCallService($scope.lastfilter);
	}
	   	
	$scope.onCancelCall = function () {
		$scope.lastfilter = "username="+$scope.username;
		$scope.lastfilter += "&idcall="+$scope.roomid;
		$scope.lastfilter += "&token="+$scope.token;
		clearParentPanel();
		callCancelCallService($scope.lastfilter);
	}
		
	var init = function () {
		$scope.username = "Testuser002";
		$scope.password = "Test123456";
	};
	

	
	// init fires after page(element?) load
	init();
		
  });
  

	
</script>
</head>
<body ng-app="myApp" ng-controller="mainController">
<form id="ajaxcall">
	Username: <input id="username" type="text" ng-model="username"/><br>
	Password: <input id="password" type="text" ng-model="password"/><br>
	Roomid: <input id="roomid" type="text" ng-model="roomid"/><br>
	Token: <input id="token" type="text" ng-model="token"/><br>
	<button type="button" name="Login" ng-click="onLogin()">Login</button><br>
	<button type="button" name="Logoff" ng-click="onLogoff()">Logoff</button><br>
	<button type="button" name="Request call" ng-click="onRequestCall()">Request call</button><br>
	<button type="button" name="Cancel call" ng-click="onCancelCall()">Cancel call</button><br>
	<button type="button" name="Get Link" ng-click="onGetLinkClick()">Get link</button><br>
</form>
<div id="maincontent">
</div>
</body>
<script>

</script>
</html>