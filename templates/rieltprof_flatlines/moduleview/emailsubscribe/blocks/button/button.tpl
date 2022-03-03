{* Шаблон блока подписки на новости *}

{addjs file="%emailsubscribe%/button.js"}

<section class="sec sec-form anti-container" id="signUpUpdate">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-lg-6">
                <div class="sec-form_description">
                    <h3>{t}Узнайте о наших событиях первыми!{/t}</h3>
                    <p>{t}Подпишитесь на последние обновления и узнавайте о новинках и специальных предложениях первыми{/t}</p>
                </div>
            </div>
            <div class="col-xs-12 col-lg-6">
                <div class="sec-form_subscribe">
                    {if $success}
                        <div class="sec-form_succes">{$success}</div>
                    {else}
                        <form class="form-inline" action="{$router->getUrl('emailsubscribe-block-subscribebutton')}" method="POST">
                            <div class="form-group"><input id="email" name="email" type="email" placeholder="{t}Напишите свой e-mail{/t}" class="form-control"></div>
                            <div class="form-group"><button type="submit" class="theme-btn_subscribe">{t}Подписаться{/t}</button></div>
                            <div class="sec-form_unsubscribe">{t}Отписаться можно будет перейдя по ссылке, указанной в каждом рассылаемом письме{/t}</div>
                            {$this_controller->myBlockIdInput()}
                        </form>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</section>