
document.addEventListener("DOMContentLoaded", function(event) { 
  onload();
});

function onload() {
	var element = document.querySelector('.elSubmit');
	element.onclick = validate;
}

function validate(event) {
	event.preventDefault();
    grecaptcha.execute();
}

var onSubmit = function(token) {
	//document.querySelector('.elSubmit').closest('form').submit();
	var element = document.querySelector('.elSubmit');
	element.onclick = null;
	element.click();
};