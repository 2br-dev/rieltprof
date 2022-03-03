$(function() {

    /**
     * Выполняет переход к элементу, указанному в якоре
     */
    var goToTabField = function() {
        var anchor = location.hash;
        var regex = /\#tab-([\d]+)(-(.*))?/ig;
        var actionRegex = /action~(\d+)/ig;
        var goTo = regex.exec(anchor);

        if (goTo) {
            var tabN = goTo[1];
            var fieldName = goTo[3];

            $('.formbox > .rs-tabs > .tab-nav > li').eq(tabN).find('> a').click();

            if (fieldName) {
                var actionData = actionRegex.exec(fieldName);
                var element, wrapperElement, hilightElement;
                if (actionData) {
                    element = $('.tools-column li[data-id="' + actionData[1] + '"]');
                    hilightElement = element;

                } else {
                    element = $('.formbox [name="' + fieldName + '"]');
                    if (!element.length) {
                        element = $('.formbox [name="' + fieldName + '[]"]');
                    }

                    hilightElement = element.closest('tr').find('.otitle');
                }

                if (element.length) {
                    element.get(0).scrollIntoView({
                        block: 'center',
                        inline: 'center'
                    });

                    hilightElement.addClass('goto-highlight');
                }
            }
        }
    };

    goToTabField();
});