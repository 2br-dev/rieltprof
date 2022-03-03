{* Список тем поддержки в личном кабинете *}

{addjs file="rs.support.js"}
{if $list}
    <div class="page-responses">
        <div class="tab-content">
            <h2 class="h2">{t}Темы сообщений{/t}</h2>
            {foreach $list as $item}
            <div class="t-response_wrapper" data-id="{$item.id}">
                <div class="t-response_title">
                    <p>
                        <a href="{$router->getUrl('support-front-support', [Act=>"viewTopic", id => $item.id])}">{$item.title}</a>
                        <a href="{$router->getUrl('support-front-support', ["Act" => "delTopic", "id" => $item.id])}" class="t-response_delete" title="{t}Удалить переписку по этой теме{/t}"></a>
                    </p>
                    <small>{$item.updated|date_format:"%d.%m.%Y %H:%M"}</small>
                </div>
                <div class="nav-tabs_review_wrapper">
                    <div class="nav-tabs_review_comment">
                        <p>{if $first=$item->getFirstMessage()}
                                {$first.message}
                        {/if}</p>
                    </div>
                    <div class="nav-tabs_review_response">
                        <div class="comments">
                            <span>
                                <i class="i-svg i-svg-commenting"></i>
                                <span>{t}Сообщений{/t}: {$item.msgcount}{if $item.newcount>0} <strong>({t}новых{/t}: {$item.newcount})</strong>{/if}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
{/if}

<div class="page-responses form-style">
    <h2 class="h2">{t}Написать в поддержку{/t}</h2>

    {if $errors = $supp->getNonFormErrors()}
    <div class="page-error">
        {foreach $errors as $item}
            <div class="item">{$item}</div>
        {/foreach}
    </div>
    {/if}

    <form method="POST">
        <div class="form-group">
            <label class="label-sup">{t}Тема{/t}</label>

            {if count($list)>0}
                <select class="select" name="topic_id" id="topic_id">
                    {foreach from=$list item=item}
                        <option value="{$item.id}" {if $item.id == $supp.topic_id}selected{/if}>{$item.title}</option>
                    {/foreach}
                    <option value="0" {if $supp.topic_id == 0}selected{/if}>{t}Новая тема...{/t}</option>
                </select><br>
            {/if}
        </div>

        <div class="form-group" id="newtopic" {if $supp.topic_id>0}style="display:none"{/if}>
            <label class="label-sup">{t}Название новой темы{/t}</label>
            {$supp->getPropertyView('topic')}
        </div>

        <div class="form-group">
            <label class="label-sup">{t}Вопрос{/t}</label>
            {$supp->getPropertyView('message')}
        </div>

        <div class="form__menu_buttons">
            <button type="submit" class="link link-more">{t}Отправить{/t}</button>
        </div>
    </form>
</div>