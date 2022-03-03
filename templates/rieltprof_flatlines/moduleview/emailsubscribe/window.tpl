<div class="form-style modal-body">
    {if $success}
        <h2 class="h2">{t}Спасибо, что вы с нами!{/t}</h2><br>
        <div class="page-success-text">
           {$success} 
        </div>
    {else}
        <form action="{$router->getUrl('emailsubscribe-front-window')}" method="POST" class="formStyle">
            {$this_controller->myBlockIdInput()}
                <h1 class="h2">{t}Будьте в курсе наших выгодных предложений{/t}</h1>
                <p class="desc">{t}Укажите контактный Email, если Вы хотите быть в курсе наших новостей и получать сведения о скидках и акциях.{/t}</p>
                {if $errors}
                    {foreach $errors as $error}
                        <div class="page-error">
                          {$error}  
                        </div>
                    {/foreach}
                {/if}
                <div class="form-group">
                    <input type="text" name="email" placeholder="user@example.com" class="email"/>
                </div>

                <div class="form__menu_buttons">
                    <button type="submit" class="link link-more">{t}Подписаться{/t}</button>
                </div>
        </form>
    {/if}
</div>