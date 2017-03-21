document.addEventListener("DOMContentLoaded", function(event) { 
  onload();
});

function onload() {
	var element = document.querySelector('.g-recaptcha').closest('form').querySelector('[type=submit]');
	element.onclick = validate;
}

function validate(event) {
	event.preventDefault();
    grecaptcha.execute();
}

var onSubmit = function(token) {
	var element = document.querySelector('.g-recaptcha').closest('form').querySelector('[type=submit]');
	element.onclick = null;
	element.click();
};