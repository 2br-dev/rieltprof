{include file=$elem.__grid_system->getOriginalTemplate() field=$elem.__grid_system}
<div id="change-grid-warning" style="display:none">
    <br>
    <div class="notice-box" style="width:500px">
        {t alias = "При смене сеточного фреймворка"}<b>Внимание!</b> При смене сеточного фреймворка, вся информация о страницах, контейнерах,
        секциях, блоках будет потеряна. Выбор сеточного фреймворка необходимо производить перед началом разработки темы оформления.{/t}
    </div>
</div>
<script language="text/javascript">
    $(function() {
        var select = $('select[name="grid_system"]');
        var source_value = select.val();
        select.change(function() {
            $('#change-grid-warning').toggle($(this).val() != source_value);
            $(this).trigger('contentSizeChanged');
        });
    });
</script>