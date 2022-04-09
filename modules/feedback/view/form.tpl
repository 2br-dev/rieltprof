{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}
    {if $form.id && $form.public}
        {$form.title}
    {else}
        Форма не найдена
    {/if}
{/block}
{block "body"}
    <div class="col-12">
        {if $success}
            <div class="formResult success">
                {$form.successMessage|default:t("Благодарим Вас за обращение к нам. Мы ответим вам при первой же возможности.")}
            </div>
        {else}
            {if $form.id && $form.public}
                <form method="POST" enctype="multipart/form-data" action="{urlmake}">
                    {csrf}
                    {$this_controller->myBlockIdInput()}
                    <input type="hidden" name="form_id" value="{$form.id}"/>
                    {assign var=fields value=$form->getFields()}

                    {if $error_fields}
                        <div class="pageError mb-4">
                            {foreach from=$error_fields item=error_field}
                                {foreach from=$error_field item=error}
                                    <div class="invalid-feedback d-block">{$error}</div>
                                {/foreach}
                            {/foreach}
                        </div>
                    {/if}

                    <table class="formTable tabFrame">
                        <tbody>
                        {foreach from=$fields item=item key=key}
                            <div class="mb-3">
                                <label class="form-label" for="{$item.alias}">{$item.title}</label>
                                {if $item.required}
                                    <span class="text-danger">*</span>
                                {/if}
                                {$item->getFieldForm(['id' => $item.alias])}
                            </div>
                        {/foreach}
                        </tbody>
                    </table>
                    <div>
                        <span class="text-danger">*</span> - {t}Поля обязательные для заполнения{/t}
                    </div>
                    <div class="feedbkButtonLine">
                        {if $CONFIG.enable_agreement_personal_data}
                            {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Отправить{/t}"}
                        {/if}
                        <div class="mt-lg-5 mt-4">
                            <button class="btn btn-primary col-12 col-sm-auto" type="submit">Отправить</button>
                        </div>
                    </div>
                </form>
            {else}
                <p>{t}Формы с таким id не существует. Или id указан неправильно.{/t}</p>
            {/if}
        {/if}
    </div>
{/block}