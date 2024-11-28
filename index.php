<?php require './nudges.php'; ?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
	<title>*nudge*</title>
	<meta name="Description" content="*nudge*">
	<meta name="apple-mobile-web-app-title" content="*nudge*">
	<meta name="application-name" content="*nudge*">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="HandheldFriendly" content="True">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#6B6B6B">
	<link rel="shortcut icon" type="image/x-icon" href="clock.svg">
	<link rel="icon" type="image/x-icon" href="clock.svg">
	<style>
		body, html { margin: 0; padding: 0; color: #111; background-color: #A3A3A3; width: 100%; height: 100%; }
		main { margin: auto; width: fit-content; max-width: calc(100% - 4em); }
			h1 { margin: 0; padding-top: 2em; font-size: 1.5em; font-style: italic; }
			ul { list-style: none; margin: 0; padding: 0; }
				li { background-color: #CECECE; margin: 0.5em 0 0.5em 0; padding: 0.5em; border-radius: 4px; cursor: pointer; }
			details { border-top: 1px dashed #000; margin-top: 4em; padding-top: 2em;  }
				summary { cursor: pointer;}
				pre { color: #333; font-style: italic; }
	</style>
</head>
<body>
	<main>
		<h1>nudge - a gentle repeating task reminder system</h1>
		<ul id="list">
		</ul>
		<details>
			<summary>cron rules</summary>
			<pre><?= print_r($nudges, TRUE); ?></pre>
		</details>
	</main>
	<audio id="ping" src="ping.mp3"></audio>
	<script>
	"use strict";
	const todolist = '<?= json_encode($nudges); ?>';
	const todoJSON = JSON.parse(todolist);
	const sleepMS = (delay) => new Promise((resolve) => setTimeout(resolve, delay));
	function zeroPad(i) { if (i < 10) { i = "0" + i; } return i; }

	let manual, msg, yyyy, mm, dd, DoW, hh, ii, ss;
	//let audio = new Audio('ping.mp3'); // https://pixabay.com/sound-effects/level-up-3-199576/
	let audio = document.getElementById('ping'); // https://pixabay.com/sound-effects/level-up-3-199576/
	let ul = document.getElementById("list");

	function delItem() {
		this.remove();
	}

	function addMinutes(minutes) {
	 //return new Date(getTime() + minutes*60000);
		//var exp = new Date(getTime() + minutes*60000);
		
		//console.log("Expire: "++" (+"+minutes+")");
	}

	function checkItem(row) {
		const regex = new RegExp(`^(\\*|[0-9,]*${ii},?[0-9,]*) (\\*|[0-9,]*${hh},?[0-9,]*) (\\*|[0-9,]*${dd},?[0-9,]*) (\\*|[0-9,]*${mm},?[0-9,]*) (\\*|[0-7,]*${DoW},?[0-7,]*)$`);
		if(regex.test(row['cron'])) {
			var li = document.createElement("li");
			if(row['exp'] != '') { addMinutes(row['exp']); } //li.setAttribute('data-expire', ); }
			li.innerHTML = "\t\t\t"+'<input type="checkbox"> '+hh+":"+ii+" - "+row['desc'];
			ul.appendChild(li);
			li.addEventListener('click', delItem, false);
			msg += "\n  "+hh+":"+ii+" - "+row['desc']; // Collect all messages for one notification.
		}
	}

	function onePingOnlyVasily(notifyMsg) {
		// https://devdoc.net/web/developer.mozilla.org/en-US/docs/WebAPI/Using_Web_Notifications.html
		if (Notification.permission !== 'denied') {
			Notification.requestPermission(function (permission) {
				if (permission === "granted") {
					var options = { icon: 'clock.svg', }
					var ping = new Notification(notifyMsg, options);
					//setTimeout(ping.close.bind(n), 5000);
				}
			});
		}
		audio.play();
	}

	async function checkCron(manual) {
		var now = new Date();
		yyyy = now.getFullYear();
		mm = zeroPad(now.getMonth()+1);
		dd = zeroPad(now.getDate());
		DoW = now.getDay();
		hh = zeroPad(now.getHours());
		ii = zeroPad(now.getMinutes());
		ss = zeroPad(now.getSeconds());
		msg = "Nudge: ";
		todoJSON.forEach(checkItem);
		if(msg !== "Nudge: ") { onePingOnlyVasily(msg); }
		if(manual == 'manual') {
			if(ss != "00") {
				await sleepMS((60 - ss) * 1000);
				checkCron('cron');
			}
			setInterval(() => { checkCron('cron'); }, 60000);
		}
	}
	checkCron('manual');
</script>
</body>
</html>
