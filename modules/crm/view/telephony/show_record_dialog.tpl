<div class="middlebox">
    <div class="notice m-b-20">
        {t}Записи разговоров на диске занимают:{/t}<strong id="record-size"><img src="{$Setup.IMG_PATH}/adminstyle/small-loader.gif"></strong>
    </div>
</div>

<script>
    $(function() {
        $('[name="delete_all"]').change(function() {
            $('[name="delete_before_date"]').prop('disabled', $(this).is(':checked'));
        }).change();

        $.ajaxQuery({
            loadingProgress:false,
            url:'{adminUrl do="GetRecordsTelephonySize"}',
            success: function(response) {
                if (response.success) {
                    $('#record-size').text(response.record_size);
                }
            }
        });
    });
</script>