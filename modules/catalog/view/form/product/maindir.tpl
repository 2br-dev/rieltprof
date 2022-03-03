<select name="maindir" id="maindir" data-selected="{$elem.maindir}">
    <option value="">-- {t}не выбрано{/t} --</option>
</select>

<script>
$(".tree-select[data-form-name='xdir[]']").on('treeSelectChange', onDirChange);

function onDirChange(e, firstRun )
{
    var xdir = $(".tree-select[data-form-name='xdir[]']");
    var maindir = $('#maindir');
    
    maindir.html('');
    var selected = $(".tree-select_selected-value-item", xdir);
    if (selected.length == 0) {
        maindir.append('<option value="">-- {t}не выбрано{/t} --</option>');
    }
    selected.each(function() {
        let cur = $(this);
        let fulloption = '';
        $('.tree-select_selected-value-item_title-path-part', cur).each(function () {
            fulloption += $(this).html() + ' > ';
        });
        fulloption += $('.tree-select_selected-value-item_title-end-part', cur).html();

        maindir.append('<option value="'+cur.data('id')+'">' + fulloption + '</option>');
    });
    var main_selected = (firstRun) ?  maindir.attr('data-selected') : $('#maindir option:first').val();
    maindir.val(main_selected);
}

onDirChange(null, true);

</script>