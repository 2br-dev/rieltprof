{include file=$field->getOriginalTemplate()}
{literal}
<script type="text/javascript">
    $(function() {
        
        //Навесим события
        
        /**
        * Тип отображения
        */
        $('[name="show_type"]').change(function(){  
            if ($(this).val()=='string'){ //если строка
                $('[name="use_mask"]').closest('tr').show(); 
                $('[name="length"]').closest('tr').show(); 
                $('[name="use_mask"]').change();
            }
            
            if($(this).val()=='list'){ //Список
                $('[name="anwer_list"]').closest('tr').show();
                $('[name="show_list_as"]').closest('tr').show();
            }
            
            if($(this).val()=='file'){ //Файл
                $('[name="file_size"]').closest('tr').show();
                $('[name="file_ext"]').closest('tr').show();
            }
            
            
            //Если не строка, то скроем лишнее
            if ($(this).val()!='string'){
               $('[name="use_mask"]').closest('tr').hide();
               $('[name="mask"]').closest('tr').hide(); 
               $('[name="error_text"]').closest('tr').hide();
               $('[name="length"]').closest('tr').hide();
            }
            
            //Если не список, то скроем лишнее
            if ($(this).val()!='list'){
               $('[name="anwer_list"]').closest('tr').hide();
               $('[name="show_list_as"]').closest('tr').hide();
            }
            
            //Если не файл, то скроем лишнее
            if ($(this).val()!='file'){
               $('[name="file_size"]').closest('tr').hide();
               $('[name="file_ext"]').closest('tr').hide();
            }
        });
        
        
        /**
        * Использовать маску
        */
        $('[name="use_mask"]').change(function(){
            if ($(this).val()!==''){ //Если кроме значения "не проверять"
              $('[name="mask"]').closest('tr').show();  
              $('[name="error_text"]').closest('tr').show(); 
              switch($(this).val()){
                  case 'email': //Email
                        $('[name="mask"]').val('^[a-zA-Z0-9_\\-.]+@[a-zA-Z0-9\\-]+\\.[a-zA-Z0-9\\-.]+$');
                        break;
                  case 'phone': //Телефон
                         $('[name="mask"]').val('^((\\d|\\+\\d)[\\- ]?)?(\\(?\\d{3}\\)?[\\- ]?)?[\\d\\- ]{7,10}$');
                        break;
                  default:
                        $('[name="mask"]').val('');
                        break;
              } 
              
            }else{
              $('[name="mask"]').closest('tr').hide();   
              $('[name="error_text"]').closest('tr').hide();   
            }
        });
        
        
        
        /**
        * Функция проверки отображения полей поля формы
        */
        var checkDepend = function() {
            
            //Сначала закрое, всё что надо скрыть
            $('[name="anwer_list"]').closest('tr').hide();
            $('[name="show_list_as"]').closest('tr').hide();
            $('[name="use_mask"]').closest('tr').hide();
            $('[name="length"]').closest('tr').hide();
            $('[name="mask"]').closest('tr').hide();
            $('[name="error_text"]').closest('tr').hide();
            $('[name="file_size"]').closest('tr').hide();
            $('[name="file_ext"]').closest('tr').hide();
            
            //Проверим условия отображения полей 
            $('[name="show_type"]').change();
            $('[name="use_mask"]').change();
            
        }();
        
        
        
    });
</script>
{/literal}