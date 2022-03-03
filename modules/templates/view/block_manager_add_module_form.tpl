<div class="titlebox">{t}Выберите блок, который желаете вставить{/t}</div>

    <div id="admin-module-blocks" class="module-blocks">
        <div class="left">
            <div class="columntitle">{t}Модули{/t}</div>
            <div class="dropdown">
                <span id="current-module" class="dropdown-toggle gray-around" data-toggle="dropdown">
                    <span class="name">{t}Всё{/t}</span> <i class="caret"></i>
                </span>
                <ul class="dropdown-menu modules"  aria-labelledby="current-module">
                    <li class="act"><a data-view="all">{t}Все{/t}</a></li>
                    {foreach $controllers_tree as $mod_name => $module}
                        <li><a data-view="mod-{$mod_name}">{$module.moduleTitle}</a></li>
                    {/foreach}
                </ul>
            </div>
        </div>
        <div class="right">
            <div class="columntitle">{t}Блоки{/t}</div>
            <div id="admin-search-wrap" class="search-wrap">
                <input id="searchByBlocks" type="search" class="searchByBlocks" placeholder="{t}Введите название блока{/t}"/>
            </div>
            <div class="blocks">
                {foreach $controllers_tree as  $mod_name=>$module}
                    <div id="mod-{$mod_name}" class="block-list">
                    {if !empty($module.controllers)}
                        <div class="module-title">
                            <h4>{$module.moduleTitle}</h4>
                        </div>
                        <div class="module-list">
                            {foreach from=$module.controllers item=block}
                                <a class="item crud-add" href="{adminUrl do=addModuleStep2 block=$block.class}" data-crud-options='{ "onLoadTrigger":"addModule", "beforeCallback": "addModuleSectionInfo" }'>
                                    <div class="limiter">
                                        <span class="name module-item-title">{$block.info.title|default:$block.short_class}</span>
                                        <span class="info">{$block.info.description}</span>
                                    </div>
                                </a>
                            {/foreach}
                        </div>
                    {/if}
                </div>
                {/foreach}
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script>
    $(function() {
        let moduleBlocksList = $(".module-blocks");
        /**
         * Переключение групп
         */
        $('.modules a[data-view]', moduleBlocksList).on('click', function() {
            $('.act', $(this).closest('.modules')).removeClass('act');
            $(this).closest('li').addClass('act');

            $(".item", moduleBlocksList).removeClass('hidden');
            $("#searchByBlocks").val("");
            if ($(this).data('view') == 'all'){
                $('.module-title', moduleBlocksList).removeClass('hidden');
                $('.block-list', moduleBlocksList).removeClass('hidden');
                $("#admin-search-wrap").removeClass('hidden');
            }else{
                $('.module-title', moduleBlocksList).addClass('hidden');
                $('.block-list', moduleBlocksList).addClass('hidden');
                $('#'+$(this).data('view')).removeClass('hidden');
                $("#admin-search-wrap").addClass('hidden');
            }

            $('#current-module .name').text($(this).text());
        });

        /**
         * Выбор типа показа списка.
         */
        $(".select-list-type a").on('click', function() {
            let type = $(this).data('class');
            $(".select-list-type li").removeClass('active');
            $(this).parent().addClass('active');
            return false;
        });

        /**
         * Расширяем на время оператор, чтобы был независимый поиск по литера букв
         */
        $.expr[":"].contains = $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
            };
        });

        /**
         * Ввод поисковой фразы для фильтрации модулей
         */
        $(".searchByBlocks").on('input', function() {
            let val = $.trim($(this).val()).toUpperCase();
            if (val.length > 1){
                $(".block-list", moduleBlocksList).addClass('hidden');
                $('.item', moduleBlocksList).addClass('hidden');
                $(".module-item-title:contains(" + val + ")", moduleBlocksList).each(function() {
                    $(this).closest('.block-list').removeClass('hidden');
                    $(this).closest('.item').removeClass('hidden');
                });
            }else{
                $(".block-list", moduleBlocksList).removeClass('hidden');
                $(".item", moduleBlocksList).removeClass('hidden');
            }
        });

        $(window).bind('addModule', function(e, response) {
            $('#blockListDialog').dialog('close');
            if (response.close_dialog) {
                $($.rs.updatable.dom.defaultContainer).trigger('rs-update');
            }
        });
    });

    /**
     * Добавляет необходимую дополнительную информацию для вставки блока при формировании запроса через crud options
     * @param options
     * @return
     */
    function addModuleSectionInfo(options) {
        var dialogOptions = $('#blockListDialog').dialog('option', 'crudOptions');
        if ( dialogOptions.sectionId){
            options.extraParams['section_id'] = dialogOptions.sectionId;
        }
        if ( dialogOptions.position){
            options.extraParams['position'] = dialogOptions.position;
        }
        if ( dialogOptions.blockId){ //Если есть идентификатор блока
            options.extraParams['block_id'] = dialogOptions.blockId;
        }
        if ( dialogOptions.type){ //Тип
            options.extraParams['type'] = dialogOptions.type;
        }
        if ( dialogOptions.pageId){ //id страницы
            options.extraParams['page_id'] = dialogOptions.pageId;
        }
        if ( dialogOptions.context){ //Контекст
            options.extraParams['context'] = dialogOptions.context;
        }
        return options;
    }
</script>