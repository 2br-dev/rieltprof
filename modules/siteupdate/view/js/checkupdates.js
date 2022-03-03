$.fn.checkUpdates = function( method ) {
    var $this = this;
    var check = function() {
        $('.checkUpdatesWidget').html(
            '<div class="checking">'+
            '<img src="/resource/img/adminstyle/ajax-loader.gif"><br>'+
            '<p>'+lang.t('Идет проверка обновлений')+'</p>'+
            '</div>'
        );
        var url = $this.data('checkupdateUrl');
        $.getJSON(url, function(response) {
            $this.replaceWith(response.html);
        });
    }
    
    $this.on('click', '.checkForUpdates', check);
    if ($('.checking', $this).length) check();
};  