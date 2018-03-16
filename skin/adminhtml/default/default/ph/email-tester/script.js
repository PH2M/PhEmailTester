var emailTester = {
    'searchInput'           : '',
    'searchResultContainer' : '',
    'request'               : '',
    'url'                   : '',
    'loader'                : '',
    init: function (url) {
        emailTester.url = url;
        emailTester.initVar();
        emailTester.initEventListener();
    },
    initVar: function () {
        emailTester.button                          = jQuery('#generate');
        emailTester.templateRenderContainer         = '.preview .email';
        emailTester.loader                          = jQuery('.loader');
    },
    initEventListener: function () {
        emailTester.button.on('click',function (e) {
            e.preventDefault();
            return emailTester.renderTemplate();
        });
    },
    renderTemplate: function() {
        jQuery(emailTester.templateRenderContainer).html('');
        emailTester.loader.addClass('loading');
        if(emailTester.request){emailTester.request.abort()}
        emailTester.request = jQuery.ajax({
            type: 'GET',
            url: emailTester.url,
            data: jQuery('#template').val() + jQuery('#store').val(),
            dataType: 'json'
        }).done(function( response ) {
            if(response.result === 1) {
                jQuery(emailTester.templateRenderContainer).html(response.html);
            } else {
                jQuery(emailTester.templateRenderContainer).html(response.message);
            }
            emailTester.loader.removeClass('loading')
        });
    }
};

