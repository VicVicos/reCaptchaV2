var onloadCallback = function() {

    var recaptcha = jQuery('.recaptcha');
    var public_key = recaptcha.attr('data-sitekey');
    var ids = [];

    recaptcha.each(function() {
        var id = jQuery(this).attr('id');
        ids.push(id);
    });

    jQuery.each(ids, function(index, value) {
        var widgetId = grecaptcha.render(value, {
            'sitekey' : public_key,
            'callback' : onSubmit,
            'size' : 'invisible',
        });

        var form = jQuery('#' + value).parents('form').attr('id');
        jQuery('#' + form).submit(function() {
            grecaptcha.execute(widgetId);
            return true;
        });
    });

}

var onSubmit = function(token) {
    // ...
};