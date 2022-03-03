(function($) {

    $(function() {
        $('body')
            .on('click', '.item-is-viewed.new', function() {
                var self = this;
                var url = $(this).data('viewOneUrl');
                $.ajaxQuery({
                    url:url
                });
            })
            .on('click', '.all-is-viewed', function() {
                var table = $(this).closest('table');
                var url = $(this).data('viewAllUrl');
                $.ajaxQuery({
                    url:url
                });
            });
    });

})(jQuery);