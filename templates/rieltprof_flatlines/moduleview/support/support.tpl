{* Страница поддержки в личном кабинете *}

<div class="page-responses">
    <h2 class="h2">{$topic.title}</h2>
    {foreach $list as $item}
        {$user = $item->getUser()}
        <div class="nav-tabs_review_wrapper {if $item.is_admin}admin{else}user{/if}">
            <div class="nav-tabs_review_comment">
                <p>{$item.message}</p>
            </div>

            <div class="nav-tabs_review_response">
                <div class="comments">
                    <span>
                        <i class="i-svg i-svg-commenting"></i>
                        {if $item.is_admin}
                            <span><strong>{$user.name} {$user.surname}, {t}администратор{/t}.</strong> {$item.dateof|dateformat:"%e %v %Y, в %H:%M"}</span>
                        {else}
                            <span><strong>{t}Вы писали{/t}</strong> {$item.dateof|dateformat:"%e %v %Y, в %H:%M"}</span>
                        {/if}
                    </span>
                </div>
            </div>
        </div>
    {/foreach}
</div>

<div class="page-responses form-style">
    <h2 class="h2">{t}Ответить{/t}</h2>

    {if $errors = $supp->getNonFormErrors()}
        <div class="page-error">
            {foreach $errors as $item}
                <div class="item">{$item}</div>
            {/foreach}
        </div>
    {/if}

    <form method="POST">
        <div class="form-group">
            <label class="label-sup">{t}Ваше сообщение{/t}</label>
            {$supp->getPropertyView('message')}
        </div>

        <div class="form__menu_buttons">
            <button type="submit" class="link link-more">{t}Отправить{/t}</button>
        </div>
    </form>
</div>