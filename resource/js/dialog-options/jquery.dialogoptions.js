/*
 * jQuery UI dialogOptions v1.0
 * @desc extending jQuery Ui Dialog - Responsive, click outside, class handling
 * @author Jason Day
 *
 * Dependencies:
 *		jQuery: http://jquery.com/
 *		jQuery UI: http://jqueryui.com/
 *		Modernizr: http://modernizr.com/
 *
 * MIT license:
 *              http://www.opensource.org/licenses/mit-license.php
 *
 * (c) Jason Day 2014
 *
 * New Options:
 *  clickOut: true          // closes dialog when clicked outside
 *  responsive: true        // fluid width & height based on viewport
 *                          // true: always responsive
 *                          // false: never responsive
 *                          // "touch": only responsive on touch device
 *  scaleH: 0.8             // responsive scale height percentage, 0.8 = 80% of viewport
 *  scaleW: 0.8             // responsive scale width percentage, 0.8 = 80% of viewport
 *  showTitleBar: true      // false: hide titlebar
 *  showCloseButton: true   // false: hide close button
 *
 * Added functionality:
 *  add & remove dialogClass to .ui-widget-overlay for scoping styles
 *	patch for: http://bugs.jqueryui.com/ticket/4671
 *	recenter dialog - ajax loaded content
 *
 */

// add new options with default values
$.ui.dialog.prototype.options.clickOut = false;
$.ui.dialog.prototype.options.responsive = true;
$.ui.dialog.prototype.options.scaleH = 0.95;
$.ui.dialog.prototype.options.scaleW = 0.95;
$.ui.dialog.prototype.options.showTitleBar = true;
$.ui.dialog.prototype.options.showCloseButton = true;
$.ui.dialog.prototype.options.beforeDestroy = function() {};
$.ui.dialog.prototype.options.afterDestroy = function() {};


// extend _init
var _init = $.ui.dialog.prototype._init;
$.ui.dialog.prototype._init = function () {
    var self = this;

    // apply original arguments
    _init.apply(this, arguments);

    //patch
    if ($.ui && $.ui.dialog && $.ui.dialog.overlay) {
        $.ui.dialog.overlay.events = $.map('focus,keydown,keypress'.split(','), function (event) {
           return event + '.dialog-overlay';
       }).join(' ');
    }
};
// end _init


// extend open function
var _open = $.ui.dialog.prototype.open;
$.ui.dialog.prototype.open = function () {
    var self = this;


    if ($.rs) {
        $.rs.lockBody();
    }

    //Если ширина задана в процентах
    self.optionWidth = self.element.dialog('option', 'width') + "";
    self.oParentWidth = self.element.parent().outerWidth();
    self.isPercentWidth = self.optionWidth.indexOf('%') > -1;

    // get dialog original size on open
    self.oHeight = Math.max(parseInt(self.element.dialog('option', 'height')), self.element.parent().outerHeight());
    self.isTouch = $("html").hasClass("touch");

    // responsive width & height
    var resize = function () {
        // check if responsive
        // dependent on modernizr for device detection / html.touch
        if (self.options.responsive === true || (self.options.responsive === "touch" && self.isTouch)) {

            //Перерасчитываем максимально возможную ширину экрана
            if (self.isPercentWidth ) {
                var calculatedWidth = parseInt(self.optionWidth)/100 * $(window).width();
            } else {
                var calculatedWidth = self.optionWidth;
            }
            self.oWidth = Math.max(parseInt( calculatedWidth ), self.oParentWidth);

            var elem = self.element,
                wHeight = $(window).height(),
                wWidth = $(window).width(),
                dHeight = elem.parent().outerHeight(),
                dWidth = elem.parent().outerWidth(),
                setHeight = Math.min(wHeight * self.options.scaleH, self.oHeight),
                setWidth = Math.min(wWidth * self.options.scaleW, self.oWidth);

            // check & set height
            if ((self.oHeight + 100) > wHeight || elem.hasClass("resizedH")) {
                elem.dialog("option", "height", setHeight).parent().css("max-height", setHeight);
                elem.addClass("resizedH");
            }

            // check & set width
            if ((self.oWidth + 100) > wWidth || elem.hasClass("resizedW")) {
                elem.dialog("option", "width", setWidth).parent().css("max-width", setWidth);
                elem.addClass("resizedW");
            }

            // only recenter & add overflow if dialog has been resized
            if (elem.hasClass("resizedH") || elem.hasClass("resizedW")) {
                elem.dialog("option", "position", {my: "center", at: "center", of: window});
                elem.css("overflow", "auto");
            }
        }

        // add webkit scrolling to all dialogs for touch devices
        if (self.isTouch) {
            elem.css("-webkit-overflow-scrolling", "touch");
        }
    };

    // call resize()
    resize();

    // resize on window resize
    $(window).on("resize", resize);

    self.element.on('dialogclose', function() {
        $(window).off("resize", resize);
    });

    // resize on orientation change
     if (window.addEventListener) {  // Add extra condition because IE8 doesn't support addEventListener (or orientationchange)
        window.addEventListener("orientationchange", function () {
            resize();
        });
    }

    // hide titlebar
    if (!self.options.showTitleBar) {
        self.uiDialogTitlebar.css({
            "height": 0,
            "padding": 0,
            "background": "none",
            "border": 0
        });
        self.uiDialogTitlebar.find(".ui-dialog-title").css("display", "none");
    }

    //hide close button
    if (!self.options.showCloseButton) {
        self.uiDialogTitlebar.find(".ui-dialog-titlebar-close").css("display", "none");
    }

    // close on clickOut
    if (self.options.clickOut && !self.options.modal) {
        // use transparent div - simplest approach (rework)
        $('<div id="dialog-overlay"></div>').insertBefore(self.element.parent());
        $('#dialog-overlay').css({
            "position": "fixed",
            "top": 0,
            "right": 0,
            "bottom": 0,
            "left": 0,
            "background-color": "transparent"
        });
        $('#dialog-overlay').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            self.close();
        });
        // else close on modal click
    } else if (self.options.clickOut && self.options.modal) {
        $('.ui-widget-overlay').click(function (e) {
            self.close();
        });
    }

    // add dialogClass to overlay
    if (self.options.dialogClass) {
        $('.ui-widget-overlay').addClass(self.options.dialogClass);
    }

    // apply original arguments
    _open.apply(this, arguments);
};
//end open


// extend close function
var _close = $.ui.dialog.prototype.close;
$.ui.dialog.prototype.close = function () {
    var self = this;

    // apply original arguments
    _close.apply(this, arguments);

    if ($.rs && $('.ui-widget-overlay').length == 0) {
        $.rs.unlockBody();
    }

    // remove dialogClass to overlay
    if (self.options.dialogClass) {
        $('.ui-widget-overlay').removeClass(self.options.dialogClass);
    }
    //remove clickOut overlay
    if ($("#dialog-overlay").length) {
        $("#dialog-overlay").remove();
    }
};
//end close

var _destroy = $.ui.dialog.prototype._destroy;
$.ui.dialog.prototype._destroy = function () {
    var element = this.element;
    this.options.beforeDestroy.apply(element);
    _destroy.apply(this, arguments);
    this.options.afterDestroy.apply(element);
};

var _setOption = $.ui.dialog.prototype._setOption;
$.ui.dialog.prototype._setOption = function (key, value) {
    if (key == 'originalWidth') {
        this.oWidth = parseInt(value);
    }
    if (key == 'originalHeight') {
        this.oHeight = parseInt(value);
    }
    _setOption.apply(this, arguments);
};

//Fix для TinyMCE
var _allowInteraction = $.ui.dialog.prototype._allowInteraction;
$.ui.dialog.prototype._allowInteraction = function( event ) {
    if ($(event.target).closest(".mce-window").length) {
        event.stopPropagation();
        return true;
    } else {
        return _allowInteraction.apply(this, arguments);
    }
};

//Разрешаем использовать html в title диалоговых окон
$.ui.dialog.prototype._title = function(title) {
    if (!this.options.title ) {
        title.html("&#160;");
    } else {
        title.html(this.options.title);
    }
};