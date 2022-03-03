{if !empty($errors)}
    <ul class="deliveryError">
        {foreach $errors as $error}
           <li>{$error}</li>
        {/foreach}
    </ul>
{else}
    {if !$template_id}
        <ul class="deliveryError">
            <li>{t}Укажите шаблон sheepla{/t}</li>
        </ul>
    {else}
        {$delivery_extra=$url->request('delivery_extra', 'array')} 
        <link type="text/css" media="all" rel="stylesheet" href="{$public_api_css_url}"/>

        <p>Sheepla</p>
        <div id="sheeplaInsertBlock"></div>

        <input id="sheeplaInputMap" type="hidden" name="delivery_extra[value]" value="{$delivery_extra.value}"/>
        
        <script type="text/javascript">
           $(document).ready(function(){
               
               /**
               * Фильтрует информацию о инпутах, удаляя ненужные элементы массива
               * 
               */
               function filterInputs(inputObjs)
               {
                   var resultArr = []; 
                   inputObjs.each(function(index, obj){
                       if (!(/({t}изменить|выбрать{/t})/i.test($(obj).val()))) {
                         resultArr.push($(obj).attr('name')+"="+$(obj).val());
                       }
                   }); 
                   return resultArr;
               }
               
               /**
               * Заносит данные о выбранном постомате
               *
               */
               function sendInfoAboutChoose(area)
               {
                  var childsInputs = $("#sheepla-widget-control input[name^='sheepla-widget-']", $(area));   
                  var childsSelect = $("#sheepla-widget-control select[name^='sheepla-widget-']", $(area));
                   
                  //Фильтруем мусор
                  childsInputs  = filterInputs(childsInputs);
                  childsSelect  = filterInputs(childsSelect);
                  var result    = childsInputs.concat(childsSelect);  //Склеим в результат
                   
                  var input   = $("#sheeplaInputMap");
                   
                  if ( result.length>0 ) { //Если получили информацию
                       var extraInfo = result.join("&");
                       input.val(extraInfo);
                  }
                   
                  if ( $(area).text() == '' ) { //Скроем если информация не требуется.
                     $(area).parent().hide(); 
                  } 
               }
               
               /**
               * Функция инициализации sheepla
               *
               */
               function sheeplaInicialization()
               {
                   /**
                   * Добавим выбор sheepla в соответствующий блок
                   * 
                   */
                   //Инициализируем Sheepla
                   sheepla.init({
                            apikey: '{$public_api_key}',
                            cultureId: '{$culture_id}'
                   });
                   sheepla.get_special({$template_id}, "#sheeplaInsertBlock", "", "");
                   //Навесим событие после выбора пользователем постомата
                   sheepla.user.after.ui.unlock_screen = sendInfoAboutChoose; 
                   //Навесим после прорисовки  
                   sheepla.user.after_draw_special = sendInfoAboutChoose;   
               }
               
               $("#sheeplaInsertBlock").html("<p>Please wait...</p>");
               
               $LAB.loading = true;
               $LAB
                   .script('http://code.jquery.com/jquery-migrate-1.2.0.js')
                   .script('{$public_api_js_url}').wait(function(){
                   sheeplaInicialization();
                   $LAB.loading = false;
               });
           }); 
        </script> 
    {/if}
{/if}