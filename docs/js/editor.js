function editorClass() {
	const self = this;
	
	self.draw = function() {
		self.ctx.setTransform(1, 0,0, 1, 0,0); //for the clear
		self.ctx.clearRect(0, 0, self.canvas.width, self.canvas.height);
	
		for(var i = 0; i < 32; i++) {
			const c = self.stack[i];
			if(!c) continue;
			self.ctx.drawImage(c.canvas, 0, 0);
		}
	}
	
	self.sliderbgchange = function() {
		const current = self.stack[self.stacki];
		self.createfilter(current.hue, current.saturation, current.brightness, current.alpha); //for previews
		const hue = current.hue * 360;
		const saturation = current.saturation * 100;
		document.getElementById("slider-saturation").style.background = "linear-gradient(to right, hsl(0, 0%, " + current.brightness*100 + "%), hsl(" + hue + ", 100%, 50%))";
		document.getElementById("slider-brightness").style.background = "linear-gradient(to right, rgba(0,0,0,1), hsl(" + hue + ", " + saturation + "%, " + ( 50 + (1-current.saturation) * 50 ) + "%))";
		document.getElementById("slider-alpha").style.background = "linear-gradient(to right, rgba(0,0,0,0), hsl(" + hue + ", " + saturation + "%, 50%))";
		self.alterstackcanvas();
		self.draw();
	}
	
	self.colourboxchanged = function(hex) {
		if(hex.length !== 9) return;
		if(hex[0] !== "#") return;
		var r = parseInt(hex.substring(1,3), 16);
		var g = parseInt(hex.substring(3,5), 16);
		var b = parseInt(hex.substring(5,7), 16);
		var a = parseInt(hex.substring(7,9), 16);
		
		if(isNaN(r) || isNaN(g) || isNaN(b) || isNaN(a)) return;
		r /= 255, g /= 255, b /= 255, a /= 255;

		var max = Math.max(r, g, b), min = Math.min(r, g, b);
		var h, s, v = max;

		var d = max - min;
		s = max == 0 ? 0 : d / max;

		if (max == min) {
			h = 0; //greyscale
		} else {
			switch (max) {
				case r: h = (g - b) / d + (g < b ? 6 : 0); break;
				case g: h = (b - r) / d + 2; break;
				case b: h = (r - g) / d + 4; break;
			}

			h /= 6;
		}
		
		const c = self.stack[self.stacki];
		c.hue = h;
		c.saturation = s;
		c.brightness = v;
		c.alpha = a;
		self.sliderbgchange();
	}
	
	self.changetab = function(category) {
		document.getElementById(self.category).style.display = "none";
		document.getElementById("tab-" + self.category).className = "deselected";
		self.category = category;
		document.getElementById(self.category).style.display = "";
		document.getElementById(self.category).scrollTop = 0;
		document.getElementById("tab-" + self.category).className = "selected";
	}
	
	self.reloadsliders = function() {
		["hue", "saturation", "brightness", "alpha"].forEach(function(i) {
			document.getElementById("slider-" + i).value = self.stack[self.stacki][i];
		});
			
		self.sliderbgchange();
	}
	
	self.getusedlayers = function() {
		for(var i = 0, used = 0; i < 32; i++) if(self.stack[i]) used++;
		document.getElementById("usedlayers-num").innerText = used;
	}
	
	self.changemode = function(mode) {
		document.getElementById(self.mode).style.display = "none";
		self.mode = mode;
		self.clipboard = null;
		document.getElementById("clipboard").style.display = "none";
		document.getElementById(self.mode).style.display = "";
		document.getElementById("status").innerText = {picker: "EDIT EMBLEM", main: "EMBLEM EDITOR", layer: "EDIT LAYER"}[self.mode];
		
		var i;
		if(mode === "main") {
			self.changestacki(self.stacki);
			document.getElementById("previews").className = "previews-main";
			self.stackbackup = null;
		} else {
			i = self.stack[self.stacki];
			if(i) {
				self.stackbackup = {};
				for(var dkey in i) self.stackbackup[dkey] = i[dkey];
			} else {
				self.stackbackup = null;
			}
		}
		
		if(mode === "picker") {
			self.changetab("emblems");
			if(!i) {
				self.addstack("Elite Member", self.stacki);
				document.getElementById("nametext").innerText = self.stack[self.stacki].name;
				document.getElementById("littleicon").src = self.stack[self.stacki].img.src;
				self.draw();
			}
			document.getElementById("previews").style.visibility = "hidden";
			document.getElementById("usedlayers").style.display = "none";
			document.getElementById("prompt-left").innerHTML = '<span class="hover" onclick="editor.keyfuncs({key: \'escape\'})"><span class="controls-highlight">ESC</span> Back</span>';
			document.getElementById("prompt-right").innerHTML = '';
		} else {
			self.getusedlayers();
			document.getElementById("previews").style.visibility = "inherit";
			document.getElementById("usedlayers").style.display = "";
		}
		
		if(mode === "layer") {
			self.canvas.onmousedown = self.canvasmdown;
			document.onwheel = function(e) {
				const direction = (e.deltaY < 0) ? +1 : -1; //up is bigger
				self.stack[self.stacki].scalex += 0.025 * direction;
				self.stack[self.stacki].scaley += 0.025 * direction;
				self.alterstackcanvas();
				self.draw();
			}
			
			self.canvas.style.borderColor = "#ff8000";
			self.canvas.style.backgroundImage = "linear-gradient(rgba(255, 255, 255, 0.4) 2px, transparent 2px), linear-gradient(90deg, rgba(255, 255, 255, 0.4) 2px, transparent 2px), linear-gradient(rgba(255, 255, 255, 0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.3) 1px, transparent 1px)";
			self.reloadsliders();
			document.getElementById("prompt-left").innerHTML = '<span class="hover" onclick="editor.keyfuncs({key: \'enter\'})"><span class="controls-highlight">ENTER</span> Confirm</span> &nbsp; <span class="hover" onclick="editor.keyfuncs({key: \'escape\'})"><span class="controls-highlight">ESC</span> Back</span>';
			document.getElementById("prompt-right").innerHTML = '<span class="hover" onclick="editor.keyfuncs({key: \'u\'})"><span class="controls-highlight">U</span> Undo changes</span>';
			document.getElementById("previews").className = "previews-layer";
		} else {
			self.canvas.onmousedown = null;
			document.onwheel = null;
			self.canvas.style.borderColor = "#555";
			self.canvas.style.backgroundImage = "";
		}
	}
	
	self.createfilter = function(h, s, v, a) {
		var r, g, b, i, f, p, q, t;
		i = Math.floor(h * 6);
		f = h * 6 - i;
		p = v * (1 - s);
		q = v * (1 - f * s);
		t = v * (1 - (1 - f) * s);
		switch (i % 6) {
			case 0: r = v, g = t, b = p; break;
			case 1: r = q, g = v, b = p; break;
			case 2: r = p, g = v, b = t; break;
			case 3: r = p, g = q, b = v; break;
			case 4: r = t, g = p, b = v; break;
			case 5: r = v, g = p, b = q; break;
		}
		
		const format = function(num) {
			num = Math.round(num * 255).toString(16).toUpperCase();
			return (num.length < 2) ? "0" + num : num;
		}
		
		document.getElementById("colourbox").value = "#" + format(r) + format(g) + format(b) + format(a);
		document.getElementById("matrix-" + self.stacki).setAttribute("values", r + " 0 0 0 0\n0 " + g + " 0 0 0\n0 0 "+ b + " 0 0\n0 0 0 " + a + " 0");
	}
	
	self.canvasmdown = function(e) {
		e.preventDefault();
		document.onmousemove = self.canvasmmove;
		document.onmouseup = (function(e) {self.canvas.buttons[(e.which === 1) ? "left" : "right"] = false; 
											if(!self.canvas.buttons.left && !self.canvas.buttons.right) document.onmousemove = null;});
		self.canvas.lastx = e.clientX;
		self.canvas.lasty = e.clientY;
		self.canvas.buttons[(e.which === 1) ? "left" : "right"] = true;
	}
	
	self.canvasmmove = function(e) {
		if(self.canvas.buttons.left && self.canvas.buttons.right) {
			self.stack[self.stacki].scalex += (e.clientX - self.canvas.lastx)/250;
			self.stack[self.stacki].scaley += (e.clientY - self.canvas.lasty)/250;
		} else if(self.canvas.buttons.left) {
			self.stack[self.stacki].x += e.clientX - self.canvas.lastx;
			self.stack[self.stacki].y += e.clientY - self.canvas.lasty;
		} else if(self.canvas.buttons.right) {
			self.stack[self.stacki].rotate = (self.stack[self.stacki].rotate + (e.clientX - self.canvas.lastx)/1.70)%360;
		} else {
			document.onmousemove = null;
			return;
		}
		
		self.canvas.lastx = e.clientX;
		self.canvas.lasty = e.clientY;
		self.alterstackcanvas();
		self.draw();
	}
	
	self.selectpreview = function(id) {
		if(self.mode !== "main") return;
		if(id === self.stacki) {
			self.changestacki(id);
			if(self.stack[self.stacki]) self.changemode("layer");
			else self.changemode("picker");
			
		} else self.changestacki(id);
	}
	
	self.changestacki = function(index) {
		document.getElementById("layer-" + self.stacki).className = "layer-preview";
		self.stacki = index;
		const newpreview = document.getElementById("layer-" + self.stacki);
		newpreview.className = "layer-preview selected";
		document.getElementById("previews").scrollLeft = newpreview.offsetLeft;
		if(self.mode === "main") {
			if(self.stack[self.stacki]) {
				document.getElementById("prompt-left").innerHTML = '<span class="hover" onclick="editor.keyfuncs({key: \'enter\'})"><span class="controls-highlight">ENTER</span> Edit Layer</span> &nbsp; <span class="hover" onclick="editor.keyfuncs({key: \'escape\'})"><span class="controls-highlight">ESC</span> Back</span>';
				document.getElementById("prompt-right").innerHTML = '<span class="hover" onclick="editor.keyfuncs({key: \'c\'})"><span class="controls-highlight">C</span> Copy</span> &nbsp; <span class="hover" onclick="editor.keyfuncs({key: \'x\'})"><span class="controls-highlight">X</span> Clear Layer</span> &nbsp; <span class="hover" onclick="editor.keyfuncs({key: \'e\'})"><span class="controls-highlight">E</span> Change Emblem</span>';
			} else {
				document.getElementById("prompt-left").innerHTML = '<span class="hover" onclick="editor.keyfuncs({key: \'enter\'})"><span class="controls-highlight">ENTER</span> Choose emblem</span> &nbsp; <span class="hover" onclick="editor.keyfuncs({key: \'escape\'})"><span class="controls-highlight">ESC</span> Back</span>';
				document.getElementById("prompt-right").innerHTML = '';
			}
		}
	}
	
	self.movelayer = function(index) {
		if((index === -1 && self.stacki === 0) || (index === +1 && self.stacki === 31)) return;
		const otheremblem = self.stack[self.stacki + index];
		const otherfilter = document.getElementById("matrix-" + (self.stacki + index)).getAttribute("values");
		
		self.stack[self.stacki + index] = self.stack[self.stacki];
		document.getElementById("matrix-" + (self.stacki + index)).setAttribute("values", document.getElementById("matrix-" + (self.stacki)).getAttribute("values"));
		document.getElementById("layer-img-" + self.stacki).src = (otheremblem) ? otheremblem.img.src : "img/empty.png";
		
		self.stack[self.stacki] = otheremblem;
		document.getElementById("matrix-" + (self.stacki)).setAttribute("values", otherfilter);
		self.changestacki(self.stacki + index);
		self.draw();
		document.getElementById("layer-img-" + self.stacki).src = self.stack[self.stacki].img.src;
	}
	
	self.keyfuncs = function(e) {
		if(document.getElementById("editor").style.visibility !== "visible") return;
		const key = e.key.toLowerCase();
		switch(self.mode) {
			case "picker":
				switch(key) {
					case "escape": {
						if(self.stackbackup) {
							self.stack[self.stacki].name = self.stackbackup.name;
							self.stack[self.stacki].img = self.stackbackup.img;
							self.stack[self.stacki].canvas = self.stackbackup.canvas;
							self.stack[self.stacki].ctx = self.stackbackup.ctx;
						} else {
							self.stack[self.stacki] = null;
						}
						self.draw();
						self.changemode("main");
						break;
					}
				}
				break;
			case "layer":
				switch(key) {
					case "d": //move layer right
						self.movelayer(+1);
						break;
					case "a": //move layer left
						self.movelayer(-1);
						break;
					case "enter":
					case "escape":
						self.changemode("main");
						break;
					case "f":
						self.stack[self.stacki].scalex *= -1;
						self.draw();
						break
					case "u":
						if(confirm("Undo changes?")) {
							for(var dkey in self.stackbackup) self.stack[self.stacki][dkey] = self.stackbackup[dkey];
							self.reloadsliders();
							self.draw();
						}
				}
				break;
				
			case "main":
				switch(key) {
					case "enter":
						self.selectpreview(self.stacki);
						break;
					case "x":
						if(self.stack[self.stacki] && confirm("Clear Layer?")) {
							document.getElementById("layer-img-" + self.stacki).src = "img/empty.png";
							self.clipboard = null;
							document.getElementById("clipboard").style.display = "none";
							self.createfilter(0, 0, 1, 1);
							self.stack[self.stacki] = null;
							self.getusedlayers();
							self.changestacki(self.stacki);
							self.draw();
						}
						break;
					case "e":
						if(self.stack[self.stacki]) self.changemode("picker");
						break;
					case "c":
						if(!self.stack[self.stacki]) return;
						self.clipboard = self.stacki;
						const img = document.getElementById("clipboard-img");
						img.src = self.stack[self.clipboard].img.src;
						img.style.filter = "url(#filter-" + self.clipboard + ")";
						document.getElementById("clipboard").style.display = "";
						break;
					case "escape":
						updateimgs(function() {document.getElementById('editor').style.visibility = 'hidden'; document.getElementById('playercard').style.visibility = 'visible';});
						break;
					case "v": {
						if(self.clipboard === null || self.stacki === self.clipboard) return;
						self.stack[self.stacki] = {};
						for(var dkey in self.stack[self.clipboard]) self.stack[self.stacki][dkey] = self.stack[self.clipboard][dkey];
						document.getElementById("layer-img-" + self.stacki).src = self.stack[self.stacki].img.src;
						document.getElementById("matrix-" + self.stacki).setAttribute("values", document.getElementById("matrix-" + self.clipboard).getAttribute("values"));
						
						self.generatestackcanvas();
						
						self.getusedlayers();
						self.draw();
						break;
					}
				}
				break;
		}
	}
	
	document.oncontextmenu = (function(e) {e.preventDefault()});
	document.onkeypress = self.keyfuncs;
	document.onkeydown = (function(e) {if(e.key === "Escape" || e.key === "Esc") self.keyfuncs({key: "escape"});});
	self.canvas = document.getElementById("canvas");
	self.canvas.buttons = {};
	self.ctx = self.canvas.getContext("2d");
	
	self.category = "type";
	self.stack = new Array(32);
	self.stacki = 0;
	self.stackbackup = "Letter A";
	self.icons = {};
	self.mode = "picker";
	self.clipboard = null;
	
	self.addstack = function(objname) {
		self.stack[self.stacki] = {
			name: objname,
			img: self.icons[objname],
			x: 150,
			y: 150,
			rotate: 0,
			hue: 0,
			saturation: 0,
			brightness: 1,
			alpha: 1,
			scalex: 1.15,
			scaley: 1.15
		};
		
		self.generatestackcanvas();
	}
	
	self.generatestackcanvas = function() {
		const canvas = document.createElement("canvas");
		canvas.width = self.canvas.width;
		canvas.height = self.canvas.height;
		self.stack[self.stacki].canvas = canvas;
		self.stack[self.stacki].ctx = canvas.getContext("2d");
		self.alterstackcanvas();
	}
	
	self.alterstackcanvas = function() {
		const c = self.stack[self.stacki];
		c.ctx.setTransform(1, 0,0, 1, 0,0); //for the clear
		c.ctx.clearRect(0, 0, c.canvas.width, c.canvas.height);
		c.ctx.filter = "url(#filter-" + self.stacki + ")";
		c.ctx.setTransform(1, 0,0, 1, c.x, c.y);
		c.ctx.rotate(c.rotate * Math.PI / 180);
		c.ctx.scale(c.scalex, c.scaley);
		c.ctx.drawImage(c.img, -128, -128);
	}
	
	self.emblempreview = function(e) {
		document.getElementById("nametext").innerText = e.target.name;
		document.getElementById("littleicon").src = e.target.src;
		
		self.stack[self.stacki].name = e.target.name;
		self.stack[self.stacki].img = self.icons[e.target.name];
		self.alterstackcanvas();
		self.draw();
	}
	
	self.loaddata = function(stack) {
		for(var i = 0; i < 32; i++) {
			document.getElementById("matrix-" + i).setAttribute("values", "1 0 0 0 0\n0 1 0 0 0\n0 0 1 0 0\n0 0 0 1 0");
			document.getElementById("layer-img-" + i).src = "img/empty.png";
		}
		
		self.stack = stack;
		self.changestacki(0);
		
		for(var i = 0, e = stack.length; i < e; i++) {
			self.stacki = i;
			const current = self.stack[self.stacki];
			editor.createfilter(current.hue, current.saturation, current.brightness, current.alpha); //for previews
			current.img = editor.icons[current.name];
			document.getElementById("layer-img-" + i).src = current.img.src;
			editor.generatestackcanvas();
		}
		
		self.stacki = 0;
		self.draw();
		updateimgs();
	}
	
	{
		var done = 0;
		var neededdone = 261;
		const elem = document.getElementById("emblemcount");
		
		var checkdone = function() {
			if((++done) >= neededdone) {
				emblemdata = null;
				loadedall();
			}
			elem.innerText = done;
		}
		
		for(var tab in emblemdata) {
			for(var i = 0, e = emblemdata[tab].length; i < e; i++) {
				var key = emblemdata[tab][i];
				self.icons[key] = new Image();
				self.icons[key].onload = checkdone;
				self.icons[key].name = key;
				self.icons[key].src = "emblems/" + emblemdata[tab][i] + ".png";
				self.icons[key].onmouseover = self.emblempreview;
				self.icons[key].onclick = (function(e){
					document.getElementById("layer-img-" + self.stacki).src = self.stack[self.stacki].img.src;
					self.changemode("layer");
				});
				document.getElementById(tab).appendChild(self.icons[key]);
			}
		}
	}
}
