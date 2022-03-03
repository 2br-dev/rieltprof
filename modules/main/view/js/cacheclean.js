(function( $ ){

    $(function() {
        $(document).on('click', '.cache_links a', function() {

            if (confirm(lang.t('Подтверждаете?'))) {
                var type = $(this).data('ctype');
                var li = $(this).parent();

                $('.success', li).addClass('hidden');
                $.ajaxQuery({
                    url: global.cleanCacheUrl,
                    data: {
                        type: type
                    },
                    success: function() {
                        $('.success', li).removeClass('hidden');
                    }
                });
            }

            return false;
        });
    });

})( jQuery );