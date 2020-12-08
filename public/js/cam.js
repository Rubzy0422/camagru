let imageAdded = false;
let stickerAdded = false;

var WIDTH = 640;
var HEIGHT = 480;

var video = document.getElementById('video');
// SNAP 
var canvas = document.getElementById('canvas');
// var sticker_canvas = document.getElementById('sticker_canvas');
var context = canvas.getContext('2d');
// Trigger photo take
var snap = document.getElementById("snap");
var streaming = false;

var ul_img = document.getElementById("uploadimg");
startVideo();

function startVideo() {
	//  GET VIDEO
	if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
		navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
			//video.src = window.URL.createObjectURL(stream);
			video.srcObject = stream;
			streaming = true;
			video.play();
		});
	}
	else if(navigator.getUserMedia) {
		navigator.getUserMedia({ video: true }, function(stream) {
			video.src = stream;
			streaming = true;
			video.play();
		}, errBack);
	}
	else if(navigator.webkitGetUserMedia) {
		navigator.webkitGetUserMedia({ video: true }, function(stream){
			video.src = window.webkitURL.createObjectURL(stream);
			video.play();
			streaming = true;
		}, errBack);
	}
	else if(navigator.mozGetUserMedia) { // Mozilla-prefixed
		navigator.mozGetUserMedia({ video: true }, function(stream){
			video.srcObject = stream;
			video.play();
			streaming = true;
		}, errBack);
	}

}

function stopStream(stream) {
	if (streaming == true)
	{
		stream.getTracks().forEach(function(track) {
			if (track.readyState == 'live') {
				track.stop();
			}
		});
	}
}



var loadFile = function(event) {
	// Stop Video Stream
	video.hidden = true;
	stopStream(video.srcObject);

	canvas.height = HEIGHT;
	canvas.width = WIDTH;
	var file = event.target.files[0];
	
	ul_img.src = URL.createObjectURL(file);
	ul_img.onload = imageIsLoaded;
	canvas.style ="";
};

function ScaleImage(srcwidth, srcheight, targetwidth, targetheight, fLetterBox) {

	var result = { width: 0, height: 0, fScaleToTargetWidth: true };
	if ((srcwidth <= 0) || (srcheight <= 0) || (targetwidth <= 0) || (targetheight <= 0)) {
		return result;
	}
	var scaleX1 = targetwidth;
	var scaleY1 = (srcheight * targetwidth) / srcwidth;
	var scaleX2 = (srcwidth * targetheight) / srcheight;
	var scaleY2 = targetheight;
	var fScaleOnWidth = (scaleX2 > targetwidth);
	if (fScaleOnWidth) {
		fScaleOnWidth = fLetterBox;
	}
	else {
		fScaleOnWidth = !fLetterBox;
	}
	if (fScaleOnWidth) {
		result.width = Math.floor(scaleX1);
		result.height = Math.floor(scaleY1);
		result.fScaleToTargetWidth = true;
	}
	else {
		result.width = Math.floor(scaleX2);
		result.height = Math.floor(scaleY2);
		result.fScaleToTargetWidth = false;
	}
	result.targetleft = Math.floor((targetwidth - result.width) / 2);
	result.targettop = Math.floor((targetheight - result.height) / 2);
	return result;
}

snap.addEventListener("click", function() {
	video.hidden = true;

	var canvas2 = document.getElementById('canvasstickers');
	canvas2.width = WIDTH;
	canvas2.height = HEIGHT;

	canvas.height = HEIGHT;
	canvas.width = WIDTH;

	var result = ScaleImage(video.videoWidth, video.videoHeight, WIDTH, HEIGHT, true);
	context.drawImage(document.getElementById("uploadimg"), result.targetleft, result.targettop, result.width, result.height);
	context.drawImage(video, 0, 0, WIDTH, HEIGHT);
	canvas.style ="";
	stopStream(video.srcObject);
	document.getElementById("snapControll").style = "display:none";
	document.getElementById("editEnable").style= "";
	imageAdded = true;
	updateSubmit();
});

function imageIsLoaded() {
	var canvas2 = document.getElementById('canvasstickers');
	canvas2.width = WIDTH;
	canvas2.height = HEIGHT;

	var result = ScaleImage(this.width, this.height, WIDTH, HEIGHT, true);
	context.drawImage(document.getElementById("uploadimg"), result.targetleft, result.targettop, result.width, result.height);
	document.getElementById("snapControll").style = "display:none";
	document.getElementById("editEnable").style="";
	imageAdded = true;
	updateSubmit();
}

function retake() {
	// Retake / ReUpload a Image
	document.getElementById("snapControll").style = "";
	document.getElementById("editEnable").style= "display:none";
	startVideo();
	video.hidden = false;
	ul_img.src="";
	imageAdded = false;
	updateSubmit();
}

// ============================================================ STICKER CANVAS ============================================================

(function() {
	var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
	window.requestAnimationFrame = requestAnimationFrame;
})();

var imagesOnCanvas = [];

function renderScene() {
	requestAnimationFrame(renderScene);
	var stickcanvas = document.getElementById('canvasstickers');
	var ctx = stickcanvas.getContext('2d');
	ctx.clearRect(0,0,
		stickcanvas.width,
		stickcanvas.height
	);
	for(var x = 0,len = imagesOnCanvas.length; x < len; x++) {
		var obj = imagesOnCanvas[x];
		obj.context.drawImage(obj.image,obj.x,obj.y);
	}
}


requestAnimationFrame(renderScene);


	
window.addEventListener("load",function(){
	var stickcanvas = document.getElementById('canvasstickers');
	stickcanvas.onmousedown = function(e) {
		var downX = e.offsetX,downY = e.offsetY;
		// scan images on canvas to determin if event hit an object
		for(var x = 0,len = imagesOnCanvas.length; x < len; x++) {
			var obj = imagesOnCanvas[x];
			if(!isPointInRange(downX,downY,obj)) {
				continue;
			}
			startMove(obj,downX,downY);
			break;
		}
	} 
},false);

function startMove(obj,downX,downY) {
	var stickcanvas = document.getElementById('canvasstickers');

	var origX = obj.x, origY = obj.y;
	stickcanvas.onmousemove = function(e) {
		var moveX = e.offsetX, moveY = e.offsetY;
		var diffX = moveX-downX, diffY = moveY-downY;
		obj.x = origX+diffX;
		obj.y = origY+diffY;
	}

	stickcanvas.onmouseup = function() {
		// stop moving
		stickcanvas.onmousemove = function(){};
	}
}

function isPointInRange(x,y,obj) {
	return !(x < obj.x ||
		x > obj.x + obj.width ||
		y < obj.y ||
		y > obj.y + obj.height);
}


function allowDrop(e)
{
	e.preventDefault();
}

function drag(e)
{
	//store the position of the mouse relativly to the image position
	e.dataTransfer.setData("mouse_position_x",e.clientX - e.target.offsetLeft );
	e.dataTransfer.setData("mouse_position_y",e.clientY - e.target.offsetTop  );
	e.dataTransfer.setData("image_id",e.target.id);
}

function drop(e)
{
	e.preventDefault();
	var image = document.getElementById( e.dataTransfer.getData("image_id") );
	var mouse_position_x = e.dataTransfer.getData("mouse_position_x");
	var mouse_position_y = e.dataTransfer.getData("mouse_position_y");
	var stickcanvas = document.getElementById('canvasstickers');
	var ctx = stickcanvas.getContext('2d');
	
	imagesOnCanvas.push({
		context: ctx,  
		image: image,  
		x:e.clientX - stickcanvas.offsetLeft - mouse_position_x,
		y:e.clientY - stickcanvas.offsetTop - mouse_position_y,
		width: image.offsetWidth,
		height: image.offsetHeight
	});
	stickerAdded = true;
	updateSubmit();
}


function clearStickers() {
	var c = document.getElementById('canvasstickers');
	var ctx = c.getContext("2d");
	imagesOnCanvas = [];
	ctx.clearRect(0, 0,c.width, c.height); 
	stickerAdded = false;
	updateSubmit();
}


function updateSubmit() {
	if (imageAdded === true && stickerAdded === true)
	{
		document.getElementById("PostBtn").disabled = "";
		// console.log(imageAdded, stickerAdded);
	}
	else {
		document.getElementById("PostBtn").disabled = "true";
	}
}

function appendSubmit() {
	// Add submit prior to actually sending it out :)
	var form = document.getElementById('PostForm');
	
	var userimg = document.createElement("input");
	userimg.type='hidden';
	userimg.name='userimg';
	userimg.value = document.getElementById('canvas').toDataURL("image/png");
	
	var stickerimg = document.createElement("input");
	stickerimg.type='hidden';
	stickerimg.name='stickerimg';
	stickerimg.value = document.getElementById('canvasstickers').toDataURL("image/png");
	
	form.appendChild(stickerimg);
	form.appendChild(userimg);

	form.submit();
}




