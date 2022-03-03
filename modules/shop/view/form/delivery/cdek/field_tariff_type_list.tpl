<style type="text/css">
    .cdekTable td{
       vertical-align:top; 
    }
    
    .cdekTable .left{
        width:300px;
    }
    
    .cdekTable .left select{
       font-size:11px; 
    }
    
    .cdekTable .tariffList{
        width:300px;
        height:220px !important;
    }
    
    .cdekTable .center{
        vertical-align:middle;
        width:20px;
    }
    
    
    .cdekTable .right select{
        height: auto !important;
        width:350px;
        font-size:11px;
    }
    
    .cdekTable a{
        width:16px;
        height:16px;
        display:inline-block;
        margin:5px;
        cursor:pointer;
        background-position:top left;
        background-repeat:no-repeat;
    }
    
    .cdekTable .up{
        background-image:url(../../resource/img/adminstyle/arrows/arrow-up.png);
    }
    
    .cdekTable .addTariff{
        background-image:url(../../resource/img/adminstyle/arrows/arrow-left.png);
    }
    
    .cdekTable .down{
        background-image:url(../../resource/img/adminstyle/arrows/arrow-down.png);
    }
    
    .cdekTable .del{
        background-image:url(../../resource/img/adminstyle/arrows/remove.png);
    }
</style>
<table class="cdekTable">
    <tr>
        <td colspan="3"><span class="inlineError" style="visibility:hidden">{t}Добавьте в список элемент из правого списка{/t}</span></td>
    </tr>
    <tr>
        <td class="left">
            <div>
                {static_call var=list callback=['\Shop\Model\DeliveryType\Cdek2','handbookTariffList']}
                <select class="tariffList selectAllBeforeSubmit" name="data[tariffTypeList][]" multiple="multiple">
                    {$selected=$elem.tariffTypeList} 
                    {if !empty($selected)}
                        {foreach $selected as $item}
                            {foreach $list as $group=>$tariffList}
                                {if isset($tariffList.$item) }
                                    <option value="{$item}" data-group="{$group}">{$tariffList.$item}</option>
                                {/if}
                            {/foreach}
                        {/foreach} 
                    {/if}
                </select>
            </div>
        </td>
        <td class="center">
            <a class="up btn" title="{t}Поднять{/t}" style="visibility:hidden;"></a>
            <a class="addTariff" title="{t}Добавить{/t}"></a>
            <a class="down btn" title="{t}Опустить{/t}" style="visibility:hidden;"></a>
            <a class="del btn" title="{t}Удалить{/t}" style="visibility:hidden;"></a>
        </td>
        <td class="right">
            {$elem->__tariffTypeCode->formView()}
        </td>
    </tr>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        var cdekTable = $(".cdekTable");
        
        
        function checkCDEKTariffVisibility(){
            //Проверим есть ли назначенный хоть один тариф
            if ($(".tariffList option",$(".cdekTable")).length==0){
               $(".inlineError",$(".cdekTable")).css('visibility','visible'); 
            }else{
               $(".inlineError",$(".cdekTable")).css('visibility','hidden');  
            } 
        }
        
        /**
        * Удяляет варианты уже выбранные из общего списка
        *
        */
        function shiftTariffSelectRowPresent(){
            var selected = [];
            {if !empty($selected)}
                {foreach $selected as $k=>$code}
                   selected[{$k}] = {$code}; 
                {/foreach}
            {/if}
            
            if (selected.length>0){
                for(var i=0;i<selected.length;i++){
                    $(".right select option[value='"+selected[i]+"']",cdekTable).remove();
                }
            }
        }
        
        /**
        * Добавление в список тарифов с приоритетами
        *
        */
        $(".addTariff",cdekTable).on('click',function(){
            
            var option = $(".right select option:selected", cdekTable);
            //Удалим из общего списка
            if (typeof(option.data('group'))=='undefined'){
               option.data('group',option.closest('optgroup').attr('label')); 
            }
            option.appendTo($(".tariffList", cdekTable));
            //Посмотрим нужно ли показывать кнопки
            if ($(".tariffList option", cdekTable).length>0){ //Есть такого пункта нет, то добавим его
               $(".btn",cdekTable).css('visibility','visible'); 
            }else{
               $(".btn",cdekTable).css('visibility','hidden');  
            }
            checkCDEKTariffVisibility();
            return false;
        });
        
        /**
        * Удаление из списоков тарифов с приоритетами
        *
        */
        $(".del",cdekTable).on('click',function(){
            $(".tariffList option:selected", cdekTable).each(function(){
                var group = $(this).data('group');
                $(this).prependTo($(".right select optgroup[label*='"+group+"']",cdekTable));
            });
            
            //Посмотрим нужно ли показывать кнопки
            if ($(".tariffList option", cdekTable).length>0){ //Есть такого пункта нет, то добавим его
               $(".btn",cdekTable).css('visibility','visible'); 
            }else{
               $(".btn",cdekTable).css('visibility','hidden');  
            }
            checkCDEKTariffVisibility();
            return false;
        });
        
        /**
        * Перемещение в списке вверх
        *
        */
        $(".up",cdekTable).on('click',function(){
            var selected = $(".tariffList option:selected:eq(0)", cdekTable);
            var index    = selected.index();
            
            if (index>0){
               selected.insertBefore($(".tariffList option:eq("+(index-1)+")", cdekTable)); 
            }
            return false;
        });
        
        /**
        * Перемещение в списке вниз
        *
        */
        $(".down",cdekTable).on('click',function(){
            var selected = $(".tariffList option:selected:eq(0)", cdekTable);
            var index    = selected.index()+1;
            
            if (index < ($(".tariffList option", cdekTable).length)){
               selected.insertAfter($(".tariffList option:eq("+index+")", cdekTable));  
            }
            return false;
        });
        
        shiftTariffSelectRowPresent();
        checkCDEKTariffVisibility();
        
        //Посмотрим нужно ли показывать кнопки
        if ($(".tariffList option", cdekTable).length>0){ //Есть такого пункта нет, то добавим его
           $(".btn",cdekTable).css('visibility','visible'); 
        }else{
           $(".btn",cdekTable).css('visibility','hidden');  
        }
        
        
});
</script>
