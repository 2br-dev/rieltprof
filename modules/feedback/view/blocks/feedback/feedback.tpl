<div class="feedbkForm">
   {if $success}
       <div class="formResult success"> 
          {$form.successMessage|default:t("Благодарим Вас за обращение к нам. Мы ответим вам при первой же возможности.")}
       </div>
   {else}
       {if $form.id && $form.public}
           <form method="POST" enctype="multipart/form-data">
               {csrf} 
               {$this_controller->myBlockIdInput()}
               <input type="hidden" name="form_id" value="{$form.id}"/>
               {assign var=fields value=$form->getFields()} 
               <p class="feedbkFormName">{$form.title}</p>
               
               {if $error_fields}
                   <div class="pageError"> 
                   {foreach from=$error_fields item=error_field}
                       {foreach from=$error_field item=error}
                            <p>{$error}</p>
                       {/foreach}
                   {/foreach}
                   </div>
               {/if}
               
               <table class="formTable tabFrame">
                   <tbody>
                       {foreach from=$fields item=item key=key} 
                           <tr class="feedbkRow">
                                
                               <td class="title key">
                                 {$item.title}
                                 {if $item.required}
                                      <span class="required">*</span>
                                 {/if}
                               </td> 
                               <td class="fieldVals value">
                                   {$item->getFieldForm()}
                                   {if $item.hint}
                                       <div class="help">
                                           {$item.hint}
                                       </div>
                                   {/if}
                               </td>      
                           </tr>
                       {/foreach}
                       
                   </tbody>
               </table>
               <div>
                  <span class="required">*</span> - {t}Поля обязательные для заполнения{/t}
               </div>
               <div class="feedbkButtonLine">
                  {if $CONFIG.enable_agreement_personal_data}
                       {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Отправить{/t}"}
                  {/if}
                                 
                  <input type="submit" class="formSave" value="{t}Отправить{/t}"/>                  
               </div>
           </form>
       {else}
          <p>{t}Формы с таким id не существует. Или id указан неправильно.{/t}</p>
       {/if}
   {/if}
</div>
