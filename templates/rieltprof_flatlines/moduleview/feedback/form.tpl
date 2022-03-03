<div class="sec sec-content_wrapper">
   {if $success}
       <div class="form-result success">
          {$form.successMessage|default:"Благодарим Вас за обращение к нам. Мы ответим вам в самое ближайшее время."}
       </div>
   {else}
       {if $form.id}
           <form method="POST" enctype="multipart/form-data" action="{urlmake}" class="form-style">
               {csrf}
               {$this_controller->myBlockIdInput()}
               <input type="hidden" name="form_id" value="{$form.id}"/>
               <h1 class="h1">{$form.title}</h1>

               {if $error_fields}
                   <div class="page-error">
                   {foreach $error_fields as $error_field}
                       {foreach $error_field as $error}
                            <p>{$error}</p>
                       {/foreach}
                   {/foreach}
                   </div>
               {/if}

               {$fields=$form->getFields()}
               {foreach $fields as $key => $item}
                   <div class="form-group">

                       <label class="label-sup">{$item.title}
                         {if $item.required}
                              <span class="required">*</span>
                         {/if}
                       </label>

                       {$item->getFieldForm(['placeholder' => $item.hint])}
                   </div>
               {/foreach}

               <div class="req-box">
                  <span class="required">*</span> - {t}Поля обязательные для заполнения{/t}
               </div>

               {if $CONFIG.enable_agreement_personal_data}
                   {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Отправить{/t}"}
               {/if}
               <div class="form__menu_buttons">
                  <button type="submit" class="link link-more">{t}Отправить{/t}</button>
               </div>
           </form>
       {else}
          <p>{t}Формы с таким id не существует. Или id указан неправильно.{/t}</p>
       {/if}
   {/if}
</div>