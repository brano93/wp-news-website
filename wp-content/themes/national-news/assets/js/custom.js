jQuery(function($) {

    /* -----------------------------------------
    Preloader
    ----------------------------------------- */
    $('#preloader').delay(1000).fadeOut();
    $('#loader').delay(1000).fadeOut("slow");

     /* -----------------------------------------
    Rtl Check
    ----------------------------------------- */
    $.RtlCheck = function () {
        if ($('body').hasClass("rtl")) {
            return true;
        } else {
            return false;
        }
    }
    $.RtlSidr = function () {
        if ($('body').hasClass("rtl")) {
            return 'right';
        } else {
            return 'left';
        }
    }

    /* -----------------------------------------
    Flash News
    ----------------------------------------- */
    $('.marquee').marquee({
        speed: 600,
        gap: 0,
        delayBeforeStart: 0,
        direction: $.RtlSidr(),
        duplicated: true,
        pauseOnHover: true,
        startVisible: true
    });

    /*--------------------------------------------------------------
    # Banner Slider
    --------------------------------------------------------------*/
    $('.main-news-wrap').slick({
        autoplay: false,
        autoplaySpeed: 3000,
        dots: false,
        arrows: true,
        adaptiveHeight: true,
        slidesToShow: 1,
        rtl: $.RtlCheck(),
        nextArrow: '<button class="adore-arrow slide-next fas fa-angle-double-right"></button>',
        prevArrow: '<button class="adore-arrow slide-prev fas fa-angle-double-left"></button>',
    });
    
    /* -----------------------------------------
    Post Carousel
    ----------------------------------------- */
    $('.four-column.carousel-wrapper').slick({
        autoplay: false,
        autoplaySpeed: 3000,
        dots: false,
        arrows: true,
        adaptiveHeight: true,
        slidesToShow: 4,
        rtl: $.RtlCheck(),
        nextArrow: '<button class="adore-arrow slide-next fas fa-angle-double-right"></button>',
        prevArrow: '<button class="adore-arrow slide-prev fas fa-angle-double-left"></button>',
        responsive: [{
            breakpoint: 1025,
            settings: {
                slidesToShow: 4,
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
            }
        }
        ]
    });
    $('.three-column.carousel-wrapper').slick({
        autoplay: false,
        autoplaySpeed: 3000,
        dots: false,
        arrows: true,
        adaptiveHeight: true,
        slidesToShow: 3,
        rtl: $.RtlCheck(),
        nextArrow: '<button class="adore-arrow slide-next fas fa-angle-double-right"></button>',
        prevArrow: '<button class="adore-arrow slide-prev fas fa-angle-double-left"></button>',
        responsive: [{
            breakpoint: 1025,
            settings: {
                slidesToShow: 3,
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
            }
        }
        ]
    });
    $('.two-column.carousel-wrapper').slick({
        autoplay: false,
        autoplaySpeed: 3000,
        dots: false,
        arrows: true,
        adaptiveHeight: true,
        slidesToShow: 2,
        rtl: $.RtlCheck(),
        nextArrow: '<button class="adore-arrow slide-next fas fa-angle-double-right"></button>',
        prevArrow: '<button class="adore-arrow slide-prev fas fa-angle-double-left"></button>',
        responsive: [{
            breakpoint: 1025,
            settings: {
                slidesToShow: 2,
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
            }
        }
        ]
    });
    $('.one-column.carousel-wrapper').slick({
        autoplay: false,
        autoplaySpeed: 3000,
        dots: false,
        arrows: true,
        adaptiveHeight: true,
        slidesToShow: 1,
        rtl: $.RtlCheck(),
        nextArrow: '<button class="adore-arrow slide-next fas fa-angle-double-right"></button>',
        prevArrow: '<button class="adore-arrow slide-prev fas fa-angle-double-left"></button>',
    });

    /*--------------------------------------------------------------
    # Navigation menu responsive
    --------------------------------------------------------------*/
    $(document).ready(function(){
        $(".menu-toggle").click(function(){
            $(".main-navigation .nav-menu").slideToggle(500, function() {
                if ($(this).css('display') == 'none') {
                    // Remove inline class
                    $(this).css('display', '');
                }
            });
        });
    });
    $(window).on('load resize', function() {
        if ($(window).width() < 1200) {
            $('.main-navigation').find("li").last().bind('keydown', function(e) {
                if (e.which === 9) {
                    e.preventDefault();
                    $('#masthead').find('.menu-toggle').focus();
                }
            });
        } else {
            $('.main-navigation').find("li").unbind('keydown');
        }
    });

    var primary_menu_toggle = $('#masthead .menu-toggle');
    primary_menu_toggle.on('keydown', function(e) {
        var tabKey = e.keyCode === 9;
        var shiftKey = e.shiftKey;

        if (primary_menu_toggle.hasClass('open')) {
            if (shiftKey && tabKey) {
                e.preventDefault();
                $('.main-navigation').toggleClass('toggled');
                primary_menu_toggle.removeClass('open');
            };
        }
    });

    /*--------------------------------------------------------------
    # Navigation Search
    --------------------------------------------------------------*/
    var searchWrap = $('.navigation-search-wrap');
    $(".navigation-search-icon").click(function(e) {
        e.preventDefault();
        searchWrap.toggleClass("show");
        searchWrap.find('input.search-field').focus();
    });
    $(document).click(function(e) {
        if (!searchWrap.is(e.target) && !searchWrap.has(e.target).length) {
            $(".navigation-search-wrap").removeClass("show");
        }
    });

    $('.navigation-search-wrap').find(".search-submit").bind('keydown', function(e) {
        var tabKey = e.keyCode === 9;
        if (tabKey) {
            e.preventDefault();
            $('.navigation-search-icon').focus();
        }
    });

    $('.navigation-search-icon').on('keydown', function(e) {
        var tabKey = e.keyCode === 9;
        var shiftKey = e.shiftKey;
        if ($('.navigation-search-wrap').hasClass('show')) {
            if (shiftKey && tabKey) {
                e.preventDefault();
                $('.navigation-search-wrap').removeClass('show');
                $('.navigation-search-icon').focus();
            }
        }
    });

    /* -----------------------------------------
    Scroll Top
    ----------------------------------------- */
    var scrollToTopBtn = $('.national-news-scroll-to-top');

    $(window).scroll(function() {
        if ($(window).scrollTop() > 400) {
            scrollToTopBtn.addClass('show');
        } else {
            scrollToTopBtn.removeClass('show');
        }
    });

    scrollToTopBtn.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, '300');
    });

});