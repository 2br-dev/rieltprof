{$attr=$field->getAttrArray()}
<input id="sheeplaAdminApi" data-url="{$router->getAdminUrl('userAct')}" data-list-id="#sheeplaTemplates" data-list-error="#sheeplaTemplateError" name="{$field->getFormName()}" value="{$field->get()}" {if $field->getMaxLength()>0}maxlength="{$field->getMaxLength()}"{/if} {$field->getAttr()} {if !$attr.type}type="text"{/if}/>
{include file="%system%/coreobject/type/form/block_error.tpl"}

<script type="text/javascript">
   /**
   * Обновляет зону с выбором шаблонов для sheepla
   * 
   */
   function updateSheeplaTemplates(){
      var $this = $(this); 
      var val   = $.trim($this.val());
      if (val.length >= 32){ //Предохраняемся от отправки, если ключ задан не полностью 
         $.rs.loading.show();
         $.ajax({
             type : "POST",
             url  : $this.data('url'),
             data : {
                 userAct     : 'staticGetTemplatesByApiKey', //Метод которой надо выполнить 
                 deliveryObj : 'sheepla',          //Имя объекта доставки 
                 params      : {                   //Параметры для передачи в метов
                     'admin_api' : val
                 }
             },
             dataType : 'json',
             success : function(response){
                $.rs.loading.hide();
                if (response.success){ //Если запрос удался, покажем выпадающий список если он не пуст 
                   var list = response.data.list;  
                   if (list.length>0){ //Если вернулся массив с вариантами шаблонов
                      $($this.data('list-id')).empty().show();
                      $($this.data('list-error')).hide(); //Спрячем ошибку
                      for (var i=0;i<list.length;i++){
                          $($this.data('list-id')).append('<option value="'+list[i]['id']+'">'+list[i]['title']+'</option>'); 
                      }
                   }else{ //Если массив шаблонов пуст
                      $($this.data('list-id')).hide(); 
                      $($this.data('list-error')).show(); //Покажем ошибку
                   } 
                }else{ //Если нет
                   console.log("{t}Ошибка при запросе пользовательской функции{/t}",response);
                }
                
             }
         }); 
      } 
   }
   $("#sheeplaAdminApi").on('keyup',updateSheeplaTemplates);
</script>