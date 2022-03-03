{$app->autoloadScripsAjaxBefore()}
<div class="crud-ajax-group">
    {if !$url->isAjax()}
    <div id="content-layout">
        <div class="viewport">
    {/if}
            <div class="titlebox gray-around">{$elements.formTitle}</div>

            <div class="middlebox">
                <div class="crud-form-error">
                    {if count($elements.formErrors)}
                        <ul class="error-list">
                            {foreach from=$elements.formErrors item=data}
                                <li>
                                    <div class="{$data.class|default:"field"}">{$data.fieldname}<i class="cor"></i></div>
                                    <div class="text">
                                        {foreach from=$data.errors item=error}
                                        {$error}
                                        {/foreach}
                                    </div>
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                </div>

                <div class="crud-form-success text-success"></div>

                <div class="columns">
                    <div class="form-column">
                        {$elements.form}
                    </div>

                    <div class="tools-column fullheight">
                        <div class="controller_info">
                            <h3>{t}Разработчикам{/t}</h3>
                            <ul class="list-with-help">
                                <li><a href="{adminUrl mod_controller="main-routes"}" style="text-decoration:underline">{t}Маршруты в системе{/t}</a>
                                    <div class="tool-descr">{t}Позволяет проверить разработчику, какой маршрут откликается на заданный URL{/t}</div>
                                </li>
                                <li><a class="crud-get" href="{adminUrl do="reinstallCore"}" style="text-decoration:underline">{t}Переустановить таблицы ядра{/t}</a>
                                    <div class="tool-descr">{t}Переустанавливает системные таблицы и выполняет при необходимости патчи{/t}</div>
                                </li>
                                <li><a class="crud-get" href="{adminUrl do="syncDb"}" style="text-decoration:underline">{t}Исправить структуру БД{/t}</a>
                                    <div class="tool-descr">{t}Приводит структуру БД в соответствии со всеми ORM объектами в системе{/t}</div>
                                </li>
                                <li><a class="crud-edit" href="{adminUrl do="ajaxshowchangelog"}" style="text-decoration:underline">{t}История изменений{/t}</a>
                                    <div class="tool-descr">{t}Отображает историю изменений модуля по версиям{/t}</div>
                                </li>                                
                                <li><a class="crud-get" href="{adminUrl do="testMail"}" style="text-decoration:underline">{t}Проверить отправку писем{/t}</a>
                                    <div class="tool-descr">{t}Будет отправлено тестовое сообщение администратору сайта{/t}</div>
                                </li>
                                <li><a href="{adminUrl do=false mod_controller="main-blockedip"}" style="text-decoration:underline">{t}Блокировка IP-адресов{/t}</a>
                                    <div class="tool-descr">{t}Переход в раздел управления списком заблокированных IP или диапазонов IP{/t}</div>
                                </li>
                                <li><a class="crud-get" href="{adminUrl do="unlockCron"}" style="text-decoration:underline">{t}Разблокировать Cron{/t}</a>
                                    <div class="tool-descr">{t}Удалить файл блокировки планировщика заданий Cron{/t}<br>(/storage/locks/cron)</div>
                                </li>
                                {if !$smarty.const.CLOUD_UNIQ}
                                    <li><a href="{adminUrl do="phpInfo"}" style="text-decoration:underline" target="_blank">{t}Посмотреть phpinfo(){/t}</a>
                                        <div class="tool-descr">{t}Отображает текущие настройки PHP. Доступно только Супервизорам.{/t}</div>
                                    </li>
                                {/if}
                                <li><a class="crud-add" href="{adminUrl do=false mod_controller="main-systemcheck"}" style="text-decoration:underline">{t}Самотестирование{/t}</a>
                                    <div class="tool-descr">{t}Проводит тестирование серверного окружения на соответствие требованиям ReadyScript{/t}</div>
                                </li>
                                <li><a href="{adminUrl do="showEventListeners"}" style="text-decoration:underline">{t}Подписчики на события{/t}</a>
                                    <div class="tool-descr">{t}Позволяет разработчику увидеть, какие подписчики обрабатывают события системы{/t}</div>
                                </li>
                                <li><a href="{adminUrl do=false mod_controller="main-langctrl"}" style="text-decoration:underline">{t}Управление переводами{/t}</a>
                                    <div class="tool-descr">{t}Позволяет создавать файлы переводов{/t}</div>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        {if !$url->isAjax()}
            <div class="footerspace"></div>
        </div> <!-- .viewport -->
    </div> <!-- .content -->
    {/if}
    <div class="bottom-toolbar fixed">
        <div class="viewport">
            <div class="common-column">
                {if isset($elements.bottomToolbar)}
                    {$elements.bottomToolbar->getView()}
                {/if}
            </div>
        </div>
    </div>    
</div>
{$app->autoloadScripsAjaxAfter()}