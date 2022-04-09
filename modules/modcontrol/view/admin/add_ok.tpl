 <div class="mod_inst_result">
    <div class="text-success" style="display:block">
        {t}Модуль успешно установлен{/t}
    </div>
    <br>

    <p><b>{t}Дальнейшие действия:{/t}</b></p>

    <ul class="list-group">
        <li class="list-group-item"><a href="{adminUrl do="edit" mod=$module_name}" class="va-m-c"><i class="zmdi zmdi-settings f-19 m-r-5"></i> <span>{t}Перейти к настройкам модуля{/t}</span></a></li>
        <li class="list-group-item"><a href="{adminUrl do=false mod_controller="templates-blockctrl"}" class="va-m-c"><i class="zmdi zmdi-grid f-19 m-r-5"></i> <span>{t}Перейти к редактированию дизайна{/t}</span></a></li>
        <li class="list-group-item"><a href="{adminUrl do=false}" class="va-m-c"><i class="zmdi zmdi-view-list-alt f-19 m-r-5"></i> <span>{t}Перейти к списку модулей{/t}</span></a></li>
    </ul>
</div>