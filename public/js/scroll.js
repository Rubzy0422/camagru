// var wrapper = document.getElementById("wrapper");
// var content = document.getElementById("postcontent");
// wrapper.scrollTop = 0;


// function addEvent(obj,ev,fn) {
// 	if(obj.addEventListener)
// 		obj.addEventListener(ev,fn,false);
// 	else if(obj.attachEvent)
// 		obj.attachEvent("on"+ev,fn);    
// }

// // this is the scroll event handler
// function scroller() {
// 		if( typeof scroller.page == 'undefined' ) {
// 			scroller.page = 1;
// 		}

// 		// document.write(scroller.counter+"<br />");
	
// 	// // add more contents if user scrolled down enough
// 	if(wrapper.scrollTop+wrapper.offsetHeight > content.offsetHeight) {
// 		scroller.page++;
// 			var xhttp = new XMLHttpRequest();
// 			xhttp.onreadystatechange = function() {
// 			  if (this.readyState == 4 && this.status == 200) {
// 				  let doc = this.response;
// 				  document.getElementById("postcontent").innerHTML += doc;
// 			  }
// 			};
// 			console.log(scroller.page);
// 			xhttp.open("POST", "http://localhost:8080/camagru/posts/" + scroller.page, true);
// 			xhttp.send();	  
// 	}
// }
	
// 	// hook the scroll handler to scroll event
// addEvent(wrapper,"scroll",scroller);
	
