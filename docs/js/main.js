var editor;
var details = {playername: "Unknown Soldier", playerclantag: "[CLAN]", playerbg: ""};

window.onload = function() {
	window.onresize = function() {
		const frame = document.getElementById("frame");
		const scale = Math.min(window.innerWidth / frame.offsetWidth, window.innerHeight / frame.offsetHeight);
		frame.style = "transform: translate(-50%, -50%) scale(" + scale + ")";
	
		const previews = document.getElementById("previews");
		previews.style.transform = "";
		previews.style.transform = "translate(-" + ((Math.floor(previews.getBoundingClientRect().left)+1)/scale) + "px,0px)";
		previews.style.width = window.innerWidth/scale + "px";
		
		document.getElementById("preview-contain").style.width = (4260 + window.innerWidth/scale) + "px";
	}
	window.onresize();
	editor = new editorClass();
}

function alterbg() {
	if(confirm("View list of playercards hosted on this site in a new tab?")) {
		window.open("backgrounds/", "_blank");
		return;
	}
	
	const input = prompt("Enter URL for background image: (256x64)");
	if(input) {
		details.playerbg = input;
		document.getElementById("playercard-bg").src = input
	}
}

function loadedall() {
	{ //check for code in URL
		const urlParams = new URLSearchParams(window.location.search);
		if(urlParams.has("load")) {
			document.getElementById("datatext").value = urlParams.get("load");
			loaddata();
		}
	}
	
	document.getElementById("spinner").outerHTML = "";
	editor.changestacki(0);
	editor.changemode("main");
	editor.draw();
	updateimgs();
	document.getElementById("playername").innerText = details.playername;
	document.getElementById("playerclantag").innerText = details.playerclantag;
	document.getElementById("playercard-bg").src = details.playerbg;
	document.getElementById("playercard").style.visibility = "visible";
}

var canvasURL = null;
function updateimgs(func) {
	if(canvasURL) URL.revokeObjectURL(canvasURL);
	editor.canvas.toBlob(function(blob) {
		canvasURL = URL.createObjectURL(blob);
		const bigemblem = document.getElementById("bigemblem");
		if(func) bigemblem.onload = func;
		bigemblem.src = canvasURL;
		document.getElementById("smallemblem").src = canvasURL;
	});
}

function savedata() {
	var output = {};
	output.playername = details.playername;
	output.playerclantag = details.playerclantag;
	output.playerbg = details.playerbg;
	output.stack = [];
	for(var i = 0; i < 32; i++) {
		var x = editor.stack[i];
		if(x) {
			var obj = JSON.parse(JSON.stringify(x));
			delete obj.img;
			delete obj.canvas;
			delete obj.ctx;
			output.stack.push(obj);
		}
	}
	document.getElementById("datatext").value = Base64.encodeURI(pako.deflate(JSON.stringify(output), { to: 'string' }));
}

function loaddata() {
	var input;
	if(document.getElementById("datatext").value !== "") input = JSON.parse(pako.inflate(Base64.decode(document.getElementById("datatext").value), { to: 'string' }));
	else input = {playername: "Unknown Soldier", playerclantag: "[CLAN]", playerbg: "Hexed", stack: []};
	document.getElementById("playername").innerText = input.playername;
	document.getElementById("playerclantag").innerText = input.playerclantag;
	document.getElementById("playercard-bg").src = input.playerbg;
	details.playername = input.playername;
	details.playerclantag = input.playerclantag;
	details.playerbg = input.playerbg;
	editor.loaddata(input.stack);
}
