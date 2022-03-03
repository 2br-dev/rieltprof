<a class="debug-icon debug-icon-info" onclick="window.open('{adminUrl mod_controller="main-debug"
    do="showVars"
    toolgroup=$tool->getUniq()
    page_id=$tool->getPageId()
    block_id=$tool->getBlockId()}','popup{$tool->getUniq()}', 'width=1000,height=800,scrollbars=yes')" title="
Информация о блоке
{$title = $tool->getControllerTitle()}
{if $title}Название контроллера: {$title}
{/if}
Контроллер: {$tool->getControllerName()}
Модуль: {$tool->getConfig('name')}
Описание модуля: {$tool->getConfig('description')}
Класс: {$tool->getModule()->getName()}
Версия: {$tool->getConfig('version')}
Автор: {$tool->getConfig('author')}
Шаблон: {$tool->render_template}"></a>