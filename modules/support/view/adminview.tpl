{if !$url->isAjax()}
<div class="crud-ajax-group">
    <div id="content-layout">
            <div class="updatable" data-url="{urlmake}">
{/if}
            <div class="viewport"> <!-- .viewport -->
                <div class="top-toolbar">
                    <div class="c-head">
                        {$mainMenuIndex = $elements->getMainMenuIndex()}
                        <h2 class="title">
                            <span class="go-to-menu" {if $mainMenuIndex !== false}data-main-menu-index="{$mainMenuIndex}"{/if}>{$elements.formTitle}</span>
                            {if isset($elements.topHelp)}<a class="help-icon" data-toggle-class="open" data-target-closest=".top-toolbar">?</a>{/if}</h2>

                        <div class="buttons xs-dropdown place-left">
                            <a class="btn btn-default toggle visible-xs-inline-block" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" id="clientHeadButtons" >
                                <i class="zmdi zmdi-more-vert"></i>
                            </a>
                            <div class="xs-dropdown-menu" aria-labelledby="clientHeadButtons">
                                {if $elements.topToolbar}
                                    {$elements.topToolbar->getView()}
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div class="c-help notice notice-warning">
                        {$elements.topHelp}
                    </div>

                    <div class="notice clearfix m-b-15">
                        <div class="username col-sm-6">
                            {t}Переписка с пользователем:{/t}
                            <a href="{adminUrl do="edit" mod_controller="users-ctrl" id=$topic->getUser()->id}">
                                {$topic->getUser()->getFio()}
                            </a>
                        </div>
                        <ul class="legend col-sm-6">
                            <li><div class="admin"></div> <span>{t}Администратор{/t}</span></li>
                            <li><div class="user"></div> <span>{t}Пользователь{/t}</span></li>
                            <li><div class="new"></div> <span>{t}Новое сообщение{/t}</span></li>
                        </ul>
                    </div>
                </div>
            </div>


                <div class="columns">
                    <div class="common-column">

                        {$elements.beforeTableContent}

                        <div class="beforetable-line{if !isset($elements.filter) && !isset($elements.paginator)} sepspace{/if}">
                            {if isset($elements.paginator)}
                                {$elements.paginator->getView(['short' => true])}
                            {/if}                        
                            {if isset($elements.filter)}
                                {$elements.filter->getView()}
                            {/if}
                        </div>

                        <div class="clear-right"></div>
                        {if isset($elements.table)}
                            <form method="POST" enctype="multipart/form-data" action="{urlmake}" class="crud-list-form">
                                {foreach $elements.hiddenFields as $key => $item}
                                <input type="hidden" name="{$key}" value="{$item}">
                                {/foreach} 
                                {$elements.table->getView()}
                            </form>
                        {/if}
                        
                        <form method="post" action="{urlmake}">
                            <br>

                            <div class="viewport">
                                <label>{t}Ваше сообщение{/t}</label>
                                <textarea placeholder="{t}Текст сообщения{/t}" name="msg" style="width:100%; height: 100px;"></textarea>
                            </div>

                            <div class="bottom-toolbar">
                                <div class="viewport">
                                    <div class="common-column">

                                        <button class="btn btn-success">Отправить</button>
                                        <a href="{adminUrl do="closeTopic" chk=[$topic.id] mod_controller="support-topicsctrl"}" class="btn btn-warning crud-get">{t}Закрыть заявку{/t}</a>
                                        <a href="{$url->getSavedUrl('Support\Controller\Admin\TopicsCtrlindex')}" class="btn btn-default">{t}Отмена{/t}</a>

                                        {*$elements.bottomToolbar->getView()*}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> <!-- .columns -->

{if !$url->isAjax()}
            </div> <!-- .updatable -->
    </div> <!-- #content -->

</div>
{/if}
