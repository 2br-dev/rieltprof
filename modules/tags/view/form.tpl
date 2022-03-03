{addcss file="{$mod_css}mtagsblock.css" basepath="root"}

<script>
$LAB
.script("{$mod_js}blocktags.jquery.js")
.wait(function() {
    $(function() {
        $('.tags').blocktags({
            getWordsUrl: '{adminUrl mod_controller="tags-blocktags" tdo="getWords" do=false}',
            delWordUrl: '{adminUrl mod_controller="tags-blocktags" tdo="del" do=false}',
            getHelpListUrl: '{adminUrl mod_controller="tags-blocktags" tdo="getHelpList" do=false}'
        });
    });
});
</script>

<div class="tags" data-type="{$param.type}" data-linkid="{$param.linkid}">
    {if $param.linkid == 0}
        <div class="notags">
            {t}Добавлени тегов возможно только в режиме редактирования{/t}
        </div>    
    {else}
        <div class="grayblock">
                <div data-action="{adminUrl mod_controller="tags-blocktags" do=false tdo="addWords"}" class="tag_form">
                    <input type="hidden" name="link_id" value="{$param.linkid}">
                    <input type="hidden" name="type" value="{$param.type}">
                    {t}Ключевые слова{/t}
                    <span class="help-icon" title="{t}Введите ключевые слова через запятую. <br>Например: книги,классическая литература,чтение <br>Минимальная длина ключевого слова должна составлять 2 знака{/t}">?</span>
                    <input type="text" name="keywords" style="width:270px;" class="autocomplete">
                    <input type="button" value="{t}Добавить{/t}" class="btn btn-default add-btn m-5">
                </div>
        </div>
        <div class="word_container">
            {$word_list_html}
        </div>        
    {/if}
</div>