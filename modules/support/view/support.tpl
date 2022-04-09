{extends file="%THEME%/helper/wrapper/my-cabinet.tpl"}
{block name="content"}
<div class="col-lg-8 col-xl-9 col-xxl-7">
    <div class="d-inline-block mb-5">
        <a class="return-link" href="{$router->getUrl('support-front-support')}">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M14.7803 5.72846C15.0732 6.03307 15.0732 6.52693 14.7803 6.83154L9.81066 12L14.7803 17.1685C15.0732 17.4731 15.0732 17.9669 14.7803 18.2715C14.4874 18.5762 14.0126 18.5762 13.7197 18.2715L8.21967 12.5515C7.92678 12.2469 7.92678 11.7531 8.21967 11.4485L13.7197 5.72846C14.0126 5.42385 14.4874 5.42385 14.7803 5.72846Z"/>
            </svg>
            <span class="ms-2">{t}К списку обращений{/t}</span>
        </a>
    </div>
    <div class="h2 mb-5">{$topic.title}</div>
    <div class="mb-6 mb-5">
        {foreach $list as $item}
            {if $item.is_admin}
                {$user = $item->getUser()}
                <div class="d-flex align-items-end mb-lg-5 mb-md-4 mb-3">
                    <div class="lk-chat-item__adm">
                        <img height="32" width="32" src="{$THEME_IMG}/icons/chat-adm.svg" alt="">
                    </div>
                    <div>
                        <div class="lk-chat-item lk-chat-item_admin">
                            <div class="lk-chat-item__sender"><strong>{$user.name} {$user.surname}, {t}администратор{/t}</strong> — {$item.dateof|dateformat:"%e %v %!Y, в %H:%M"}</div>
                            <div>{$item.message}</div>
                        </div>
                    </div>
                </div>
            {else}
                <div class="d-flex justify-content-end mb-lg-5 mb-md-4 mb-3">
                    <div class="lk-chat-item lk-chat-item_client">
                        <div class="lk-chat-item__sender">
                            <strong>{t}Вы писали{/t}</strong> — {$item.dateof|dateformat:"%e %v %!Y, в %H:%M"}</div>
                        <div>{$item.message}</div>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
    <form method="POST">
        {if $supp->getNonFormerrors()}
            <div class="alert alert-danger">{$supp->getNonFormerrors()|join:", "}</div>
        {/if}
        <div class="mb-4">
            <label for="textarea1" class="form-label">{t}Ваше сообщение{/t}</label>
            {$supp->getPropertyView('message', ['id' => 'textarea1'])}
        </div>
        <button type="submit" class="btn btn-primary col-12 col-sm-auto">{t}Отправить{/t}</button>
    </form>
</div>
{/block}