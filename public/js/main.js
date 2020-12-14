// Vanilla JS navbar toggler for Bootstrap 4
// Source: https://blog.pagesd.info/2019/12/09/gerer-menu-hamburger-bootstrap-vanilla-js

(function () {
  "use strict";

  document.querySelectorAll("button.navbar-toggler")[0].addEventListener("click", function (event) {
	var target = this.getAttribute("data-target");
	var subbar = document.querySelectorAll(target)[0];
	subbar.className = (subbar.className + " show").replace(/ show show/, "");
  });

})();




