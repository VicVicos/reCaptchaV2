window.JoomlaInitReCaptcha2_0 = function () {
    "use strict";
    var e = document.getElementsByClassName("g-recaptcha"), t, n;
    for (var r = 0, i = e.length; r < i; r++)t = e[r], n = t.dataset ? t.dataset : {
        sitekey: t.getAttribute("data-sitekey"),
        theme: t.getAttribute("data-theme"),
        size: t.getAttribute("data-size")
    }, grecaptcha.render(t, n);
    console.log('test2');
};