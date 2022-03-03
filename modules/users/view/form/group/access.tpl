{addcss file="{$mod_css}tree.css" basepath="root"}
{addcss file="{$mod_css}groupedit.css" basepath="root"}

{if $elem.alias == 'supervisor'}
    <div style="margin-top:10px;" class="notice-box no-padd">
        <div class="notice-bg">
            {t}Супервизор имеет полные права ко всем модулям, сайтам и пунктам меню{/t}
        </div>
    </div>
{else}

<div class="switch-site-access">
    <input id="site_admin" type="checkbox" name="site_access" value="1" {if !empty($site_access)}checked{/if}>&nbsp;<label for="site_admin">{t}Включить доступ к администрированию текущего сайта{/t}</label>
</div>

<h3>{t}Доступ к пунктам меню{/t}</h3>
<br>

<div class="treeblock">
    <div class="left">
        <div class="full-access l-p-space">
            <input type="checkbox" name="menu_access[]" value="{$smarty.const.FULL_USER_ACCESS}" {if isset($menu_access[$smarty.const.FULL_USER_ACCESS])}checked{/if} id="full_user">
            <label for="full_user">{t}Полный доступ к меню пользователя{/t}</label>
        </div>
        <div class="lefttree localform" style="position:relative">
            <div class="overlay" id="user_overlay">&nbsp;</div>
            <div class="wrap">
                {$user_tree->getView(['render_all_nodes' => true])}
            </div>
        </div>
    </div>

    <div class="right">
        <div class="full-access l-p-space">
            <input type="checkbox" name="menu_admin_access[]" value="{$smarty.const.FULL_ADMIN_ACCESS}" {if isset($menu_access[$smarty.const.FULL_ADMIN_ACCESS])}checked{/if} id="full_admin">
            <label for="full_admin">{t}Полный доступ к меню администратора{/t}</label>
        </div>

        <div class="righttree localform" style="position:relative">
            <div class="overlay" id="admin_overlay">&nbsp;</div>
            <div class="wrap">
                {$admin_tree->getView(['render_all_nodes' => true])}
            </div>
        </div>
    </div>
</div> <!--Treeblock -->

<h3>{t}Права к модулям{/t}</h3>
<br>

<div class="moduleWrapper">
    <table border="0" class="rs-table">
        <thead class="stickyHead">
            <tr class="moduleRow">
                <th class="l-w-space"></th>
                <th class="column-module">{t}Модуль{/t}</th>
                <th class="column-name">{t}Название{/t}</th>
                <th class="column-descr">{t}Описание{/t}</th>
                <th class="column-access access_oneRight">
                    <span>{t}Уровень доступа{/t}</span>
                    <div class="access_radioBlock">
                        <span class="access_radio">{t}Разрешение{/t}</span>
                        <span class="access_radio">{t}Запрещение{/t}</span>
                        <span class="access_radio">{t}По умолчанию{/t}</span>
                    </div>
                </th>
                <th class="r-w-space"></th>
            </tr>
        </thead>
        <tbody class="access_item access_branch">
            <tr class="moduleRow access_oneRight">
                <td class="l-w-space"></td>
                <td class="column-module"></td>
                <td class="column-name"></td>
                <td class="column-descr"></td>
                <td class="column-access">
                    <div class="full_access_access_item">
                        <div class="access_oneRight access_oneGroup">
                            <span>{t}Все модули{/t}</span>
                            <div class="access_radioBlock">
                                <label class="access_radio">
                                    <input type="radio" name="full_access" value="allow" title="{t}Разрешение{/t}">
                                </label>
                                <label class="access_radio">
                                    <input type="radio" name="full_access" value="disallow" title="{t}Запрещение{/t}">
                                </label>
                                <label class="access_radio">
                                    <input type="radio" name="full_access" value="" title="{t}По умолчанию{/t}">
                                </label>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="r-w-space"></td>
            </tr>
            {foreach $module_list as $row}
                <tr class="moduleRow">
                    <td class="l-w-space"></td>
                    <td class="column-module">{$row.class}</td>
                    <td class="column-name">{$row.name}</td>
                    <td class="column-descr">{$row.description}</td>
                    <td class="column-access">
                        <div class="access_item access_branch">
                            <div class="access_oneRight access_oneGroup">
                                <span>{t}Весь модуль{/t}</span>
                                <div class="access_radioBlock">
                                    <label class="access_radio">
                                        <input type="radio" name="full_access[{$row.class}]" value="allow" title="{t}Разрешение{/t}">
                                    </label>
                                    <label class="access_radio">
                                        <input type="radio" name="full_access[{$row.class}]" value="disallow" title="{t}Запрещение{/t}">
                                    </label>
                                    <label class="access_radio">
                                        <input type="radio" name="full_access[{$row.class}]" value="" title="{t}По умолчанию{/t}">
                                    </label>
                                </div>
                            </div>
                            {include file="%users%/form/group/recursive_rights_branch.tpl" list=$row.right_object->getRightsTree()}
                        </div>
                    </td>
                    <td class="r-w-space"></td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>

{literal}
<script>
initCheckboxes = function(){

};
putOverlay = function(options)
{
    var _this = this;
    this.options = options;
    this.overdiv = $(this.options.overlay);
    this.checkbox = $(this.options.checkbox);
    
    this.change = function()
    {
        var checked = (_this.options.checkshow) ? this.checked : !this.checked;
        if (checked) _this.showOverlay();
        else {
            _this.overdiv.hide();
        }
    }
    
    this.showOverlay = function()
    {
        var parentHeight = this.overdiv.parent().height();
        if (parentHeight>0) this.overdiv.height(parentHeight);
        this.overdiv.show();
    }
    
    this.defaultDraw = function()
    {
        //Включаем оверлей по умолчанию, если нужно
        var checked = (this.options.checkshow) ? this.checkbox.get(0).checked : !this.checkbox.get(0).checked;
        if (checked) this.showOverlay();
    }
    
    this.defaultDraw();
    this.checkbox.change(this.change);
}

var userfull;
var adminfull;

$(function() {
    userfull = new putOverlay({checkbox: '#full_user', overlay:'#user_overlay', checkshow:true});
    adminfull = new putOverlay({checkbox: '#full_admin', overlay:'#admin_overlay', checkshow:true});

    $('.access_radio input').on('change', function(){
        $(this).closest('.access_branch').parents('.access_item').children('.access_oneRight').find('input').prop('checked', false);
        $(this).closest('.access_item').find('input[value="'+$(this).val()+'"]').prop('checked', true);
    });
});
</script>
{/literal}
{/if}
