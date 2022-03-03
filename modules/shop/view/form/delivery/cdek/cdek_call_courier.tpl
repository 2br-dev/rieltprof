<div id="dataTarget">
    <div class="titlebox">{$title}</div>

    <form class="formbox" action="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getCallCourierHTML'])}" data-target="#dataTarget" method="POST">
        <input type="hidden" name="call" value="1">
        {if $success}
            <p style="color:green;">{$success}</p>
        {/if}

        {if $errors}
            {foreach $errors as $error}
                <p style="color:red">{$error}</p>
            {/foreach}
        {/if}


        <div class="notice-box no-padd">
            <div class="notice-bg">
                {t}Время между началом ожидания курьера и его окончанием,<br/> должно составалять не менее 3-х часов{/t}
            </div>
        </div>
            
        <div class="cdekCallCourier">
            <div class="form-group">
                <p>{t}Страна{/t}:</p>
                <select id="cityRegion" name="call[SendCityCode]">
                    {$m=0}
                    {foreach $regions as $rtitle=>$region}
                        <option value="{$m}">{$rtitle}</option>
                        {$m=$m+1}
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <p>{t}Город{/t}:</p>
                {$m=0}
                {foreach $regions as $rtitle=>$region}
                    <select name="call[SendCityCode]" class="{if $m>0}hidden{/if} cityCode" {if $m>0}disabled="disabled"{/if}>
                        {foreach $region as $code=>$city}
                            <option value="{$city.code}" {if $current_city==$city.title}selected="selected"{/if}>{$city.fullname}</option>
                        {/foreach}
                    </select>
                    {$m=$m+1}
                {/foreach}
                
            </div>
            <div class="form-group">
                <p>{t}Дата ожидания курьера в формате ГГГГ-ММ-ДД{/t}:</p>
                <input id="send_date" type="text" date name="call[Date]" value="{$current_date}" size="9" maxlength="9"/>
            </div>
            <div class="form-group">
                <p>{t}Время начала и окончания ожидания курьера{/t}:</p>
                {t}с{/t} <select name="call[TimeBeg]">
                    {foreach $time_range as $range}
                        <option value="{if $range<10}0{/if}{$range}" {if $range==$time_default_start}selected="selected"{/if}>{if $range<10}0{/if}{$range}</option>
                    {/foreach}
                </select>:00 {t}по{/t} <select name="call[TimeEnd]">
                    {foreach $time_range as $range}
                        <option value="{if $range<10}0{/if}{$range}" {if $range==$time_default_end}selected="selected"{/if}>{if $range<10}0{/if}{$range}</option>
                    {/foreach}
                </select>:00
                <div class="chkline">
                   <input type="checkbox" name="call[use_lunch]" value="1" class="toggleBlock" data-class="#lunch_time"/> - {t}время обеда{/t}
                </div>
            </div>
            <div id="lunch_time" class="form-group hidden">
                <p>{t}Время начала обеда и его окончания:<br/>Указывать только, если входит во временной диапазон вызова(необязательно){/t}:</p>
                {t}с{/t} <select name="call[LunchBeg]">
                    <option value="0">--</option>
                    {foreach $time_range as $range}
                        <option value="{if $range<10}0{/if}{$range}">{if $range<10}0{/if}{$range}</option>
                    {/foreach}
                </select>:00 {t}по{/t} <select name="call[LunchEnd]">
                    <option value="0">--</option>
                    {foreach $time_range as $range}
                        <option value="{if $range<10}0{/if}{$range}">{if $range<10}0{/if}{$range}</option>
                    {/foreach}
                </select>:00
            </div>
            
            <div class="form-group">
                <p>{t}Ф.И.О. отправителя{/t}:</p>
                <input type="text" class="fio" name="call[SenderName]"/>
            </div>
            <div class="form-group">
                <p>{t}Контактный телефон отправителя{/t}:</p>
                <input type="checkbox" name="call[use_admin]" value="1" class="toggleBlock" checked="checked" data-class="#send_phone"//> - {t}использовать телефон администратора{/t}
                <div id="send_phone" class="sendPhone hidden">
                    {t}Укажите свой телефон{/t}: <input type="text" name="call[SendPhone]"/>
                </div>
            </div>
            <div class="form-group last">
                <p>{t}Комментарий{/t}:</p>
                <textarea name="call[Comment]"/></textarea>
            </div>
            <div class="tac">
                <a class="btn btn-success crud-form-save">{t}Вызвать курьера{/t}</a>
            </diV>
            <br>
        </div>
    </form>
    <script type="text/javascript">

          /**
          * Открытие и закрытие скрытых полей
          */
          $(".toggleBlock").on('click',function(){
              var toggle_class = $(this).data('class');
              $(toggle_class).toggleClass('hidden');
          }); 
          
          /**
          * При смене страны покажем города этой страны
          */
          $("#cityRegion").on('change',function(){
             var country_id = $('option:selected',$(this)).val(); 
             $(".cityCode").addClass('hidden').prop('disabled',true); 
             $(".cityCode:eq("+country_id+")").removeClass('hidden').prop('disabled',false); 
          });
      
    </script>
</div>