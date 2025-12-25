jQuery(function($) {
    
    /* -----------------------------------------
    Sticky Header
    ----------------------------------------- */
    if ( $("body").hasClass("header-fixed") ){
        const header = document.querySelector('.adore-header');
        window.onscroll = function() {
            if (window.pageYOffset > 200) {
                header.classList.add('fix-header');
            } else {
                header.classList.remove('fix-header');
            }
        };
        $(document).ready(function() {
            var divHeight = $('.adore-header').height();
            $('.header-outer-wrapper').css('min-height', divHeight + 'px');
        });
    }

});