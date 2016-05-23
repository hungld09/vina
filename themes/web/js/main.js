//plugin
jQuery.fn.topLink = function (settings) {
    settings = jQuery.extend({
        min: 1,
        fadeSpeed: 200
    }, settings);
    return this.each(function () {
        //listen for scroll
        var el = $(this);
        el.hide(); //in case the user forgot
        $(window).scroll(function () {
            if ($(window).scrollTop() >= settings.min) {
                el.fadeIn(settings.fadeSpeed);
            }
            else {
                el.fadeOut(settings.fadeSpeed);
            }
        });
    });
};

$(document).ready(function () {
    //$('[data-toggle="tooltip"]').tooltip();
    //$('input[rel="txtTooltip"]').tooltip();

    $(".main_menu>ul>li").each(function () {
        initialiseNav(this);
    });

    $(".main_menu>ul>li>a").bind("mouseenter", function () {
        hideAllNav();
        showChildNav(this);
    });

    $(".main_menu").bind("mouseleave", function () {
        hideAllNav();
        showCurrentNav();
    });

    //set the link
    $('#top-link').topLink({
        min: 400,
        fadeSpeed: 500
    });
    //smoothscroll
    $('#top-link').click(function (e) {
        e.preventDefault();
        $.scrollTo(0, 300);
    });
});

function initialiseNav(navitem) {
    //centre of this button
    var widthone = 0;

    widthone = $(navitem).outerWidth();
    widthone = widthone / 2;

    $(navitem).prevUntil('ul').each(function () {
        widthone = widthone + ($(this).outerWidth());
    });

    //width of subnav
    var widthtwo = 0;
    $(navitem).find("li").each(function () {
        widthtwo = widthtwo + ($(this).outerWidth());
    });
    widthtwo = widthtwo / 2;

    //calculate margin
    var marginvalue = 0;
    marginvalue = (widthone - widthtwo) + 125;
    if (marginvalue > 0) {
        //set left margin of first subnav item only if it isn't negative
        $(navitem).children("ul").find("li").first().css("margin-left", marginvalue);
    }
}

function hideAllNav() {
    $(".main_menu ul ul").removeClass("on fix");
    $(".main_menu ul ul").addClass("off");
}

function showChildNav(actOnMe) {
    $(".main_menu li").removeClass("MenuVisible");
    $(actOnMe).parent("li").find("ul").removeClass("off");
    $(actOnMe).parent("li").find("ul").addClass("on fix");
    $(actOnMe).parent("li").not($("li.active")).find("ul").bind("mouseenter", function () {
        $(this).parent("li").addClass("MenuVisible");

    }).bind("mouseleave", function () {
        $(this).parent("li").removeClass("MenuVisible");
    });
}

function showCurrentNav() {
    //only do this if it is currently hidden
    if ($(".main_menu li.active ul").hasClass("off")) {
        $(".main_menu li.active ul").removeClass("off");
        $(".main_menu li.active ul").addClass("on fix");
    }
}

function parseDate(str) {
    var mdy = str.split('/');
    return new Date(mdy[2], mdy[0] - 1, mdy[1]);
}

function daydiff(first, second) {
    return (second - first) / (1000 * 60 * 60 * 24)
}

/*plus days to date*/
Date.prototype.addDays = function (days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
};

function formatNumberValue(value) {
    value = value.toString().replace(/\$|\./g, '');

    if (isNaN(value))
        value = "";
    sign = (value == (value = Math.abs(value)));
    value = Math.floor(value * 100 + 0.50000000001);
    value = Math.floor(value / 100).toString();

    for (var i = 0; i < Math.floor((value.length - (1 + i)) / 3); i++)
        value = value.substring(0, value.length - (4 * i + 3)) + '.' + value.substring(value.length - (4 * i + 3));
    return value;
}