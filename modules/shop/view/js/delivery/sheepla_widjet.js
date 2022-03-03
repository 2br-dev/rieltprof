(function( $ ){
   $.sheeplaWidjetCreator = function( method ) {
        //Найдём ближайший input c выбором
        var closestRadio = $('input[name="delivery"]:eq(0)');        
        var form         = closestRadio.closest('form'); //найдем форму
       
        var defaults = {            
           publicUrl  : global['folder']+'/checkout/delivery/', //url для публичных запросов
           sheepla    : [], //Массив с объектами sheepla
           sheeplaDiv : '.sheeplaMap', //Контейнер куда будет вставлятся информация sheepla
           delimiter  : '&' //Разделитель, которым будет склеена строка
        },
        
        $this = form,
        data  = $this.data('sheeplaInfo');
        
        if (!$this.length) return;
        if (!data) { //Инициализация
            data = { options: defaults };
            $this.data('sheeplaInfo', data);
        }
        
        //public
        var methods = {
            /**
            * Инициализация плагина
            * 
            * @param initoptions - введённые пераметра для записи или перезаписи
            */
            init: function(initoptions) 
            {
                data.options = $.extend(data.options, initoptions);
                
                //Навесим события на переключатели
                presetSheeplaRadioEvents();
                
                //Событие перед отправкой
                $($this).on('submit',function(){
                    return checkSheeplaSubmit();
                });
                
                
                //Если контент обновился (для заказа на одной странице)
                $('body').on('new-content',function(){
                    //Навесим события на переключатели
                    presetSheeplaRadioEvents();
                    
                    //Проверим выбор sheepla и если она выбрана, то вызовем соответсвующий метод
                    var selectedCheckbox = $('input[name="delivery"]:checked');
                    if (typeof(selectedCheckbox.data('sheepla-div-id'))!='undefined'){ 
                      setOrClearSheeplaInfo(null, selectedCheckbox); 
                    }
                });
            },
            
            /**
            * Стартует объект sheepla, подгружает всё необходимое
            * 
            * @param sheeplaObj  - объект с функциями sheepla
            * @param sheeplaInfo - доп. информация для старта объекта
            */
            startSheepla : function (checkBoxObj, sheeplaInfo)
            {       
                var sheeplaObj = $.extend({},sheepla);  //Отклонируем объект, для независимости
                
                data.options.sheepla.push(sheeplaObj);
                data.options.sheepla[data.options.sheepla.length-1].init({
                    apikey: sheeplaInfo['apikey'],
                    cultureId: sheeplaInfo['cultureId']
                });
                //Инициализация
                data.options.sheepla[data.options.sheepla.length-1].get_special(sheeplaInfo['templateId'], sheeplaInfo['divId'], sheeplaInfo['userEmail'], sheeplaInfo['user']);
                
                //Навесим событие после выбора пользователем постомата
                data.options.sheepla[data.options.sheepla.length-1].user.after.ui.unlock_screen = sendInfoAboutChoose; 
                //Навесим после прорисовки  
                data.options.sheepla[data.options.sheepla.length-1].user.after_draw_special = sendInfoAboutChoose;   
                checkBoxObj.data('sheepla-start',1);  
            }
        }
        
        //private
        
        /**
        * Срабатывает при нажатии на выбранную доставку.
        * Если у этой доставки есть признак что sheepla подгружена и есть признак, что это sheepla,
        * то ничего не делаем. Если нет признака, что подгружалось, 
        * 
        * @param Event - объект события
        * @param item - объект радиокнопки
        */
        var setOrClearSheeplaInfo = function (Event, item) 
        {
            if (Event === null){ //Если вызвали программно
                var $_this = item;
            }else{ //Если событие
                var $_this = $(this);
            }
            
            var dataSheeplaDiv = $_this.data('sheepla-div-id');
            if ( typeof(dataSheeplaDiv) != 'undefined' ) { //Если это радиокнопка sheepla
               //Смотрим признак, что sheepla ещё в контейнере не стартовала
               var sheeplaInfo = $_this.data('sheepla-info'); //Вся информация
               if ( typeof($_this.data('sheepla-start')) == 'undefined' ) { //Если не стартовала
                  
                  methods.startSheepla($_this, sheeplaInfo);  //Стартуем 
                  
               }             
            }
        },
        
        
        /**
        * Навешивает события на переключатели
        * 
        */
        presetSheeplaRadioEvents = function (){
            //Пройдёмся по контейнерам sheepla
            $(data.options.sheeplaDiv, $this).each(function(i){
                 //Найдём и отметим подходящие радиокнопки, которые относятся к sheepla  
                 var deliveryId = $(this).data('delivery-id');
                 var radio      = $('input[name="delivery"][value="'+deliveryId+'"]');
                 //Перенесём данные к выборанной радио кнопке, чтобы можно было манипулировать
                 radio.data('sheepla-div-id',"#"+$(this).attr('id'));
                 radio.data('sheepla-info',$(this).data('sheepla-info'));   
            });
            
            //Получим все радио кнопки
            var radioboxes = $('input[type="radio"]',$this); //найдем radio
            
            //Навесим переключение радиокнопок 
            radioboxes.each(function(){
                $(this).on('click', setOrClearSheeplaInfo);
                var dataSheeplaDiv = $(this).data('sheepla-div-id');
                if ( $(this).prop('checked') && typeof(dataSheeplaDiv) != 'undefined' ) { //Если радиокнопка выбрана, и это sheepla
                    var sheeplaInfo = $(this).data('sheepla-info'); //Вся информация
                    methods.startSheepla($(this), sheeplaInfo);  //Стартуем 
                }
            });
        },
        
        
        
        /**
        * Срабатывает после выбора постомата ползователем, когда информация добавлена о его выборе
        * собирает полученную информацию и шлёт запрос на запись.
        * 
        * @param area - объект в который попадает информация
        */
        sendInfoAboutChoose = function (area)
        {
           var childsInputs = $("#sheepla-widget-control input[name^='sheepla-widget-']", $(area));   
           var childsSelect = $("#sheepla-widget-control select[name^='sheepla-widget-']", $(area));
           
           //Фильтруем мусор
           childsInputs  = filterInputs(childsInputs);
           childsSelect  = filterInputs(childsSelect);
           var result    = childsInputs.concat(childsSelect);  //Склеим в результат
           
           var inputId = $(area).data('input-id');
           var input   = $(inputId);
           
           if ( result.length>0 ) { //Если получили информацию
               var extraInfo = result.join(data.options.delimiter);
               input.val(extraInfo);
           }
           
           if ($(area).text() == '') { //Скроем если информация не требуется.
               $(area).parent().hide(); 
           }
        },
        
        /**
        * Проверяет выбрана ли sheepla перед отправкой и выбран ли постомат, если он действительно нужен
        * 
        */                                  
        checkSheeplaSubmit = function()
        {
            
           var checkedInput   = $("input[name='delivery']:checked");  
           var dataSheeplaDiv = checkedInput.data('sheepla-div-id');
           var deliveryId     = checkedInput.val();
           
           if ( typeof(dataSheeplaDiv) != 'undefined' ) { //Если это радиокнопка sheepla
                sendInfoAboutChoose($(dataSheeplaDiv));
                if (!checkSheeplaChoose($(dataSheeplaDiv))){
                    alert('Выберите пункт доставки для получения.');
                    return false;
                }
                $("#sheeplaInputMap"+deliveryId).prop('disabled',false);  
                
           }
           return true; 
        }, 
        
        /**
        * Проверяет, а выбрано, ли что либо в оборачивающем диве, если
        * если из чего выбирать. Возвращет true, если можно продолжать
        * 
        * @return boolean
        */
        checkSheeplaChoose = function(dataSheeplaDiv)
        {  
           //Если это доставка без выбора пункта забора                         
           if ($('input[type="button"]',dataSheeplaDiv).length == 0){
             return true;   
           }                                                      
           var flag = false;
           //Пройдемся по скрытым полям и посмотрим задано(выбрано) ли значение
           $("#sheepla-widget-control input[type='hidden'][readonly='readonly']",dataSheeplaDiv).each(function(i){
               if ($(this).val().length>0){
                   flag = true;
               }
           });
           //Пройдемся по селектам и посмотрим задано(выбрано) ли значение
           $("#sheepla-widget-control select[name^='sheepla-widget-']",dataSheeplaDiv).each(function(i){
               if ($(this).val().length>0){
                   flag = true;
               }
           });
           return flag;
        },
        
        /**
        * Фильтрует информацию о инпутах, удаляя ненужные элементы массива
        * 
        */
        filterInputs = function(inputObjs)
        {
           var resultArr = []; 
           inputObjs.each(function(index,obj){
               if (!(/(изменить|выбрать)/i.test($(obj).val()))) {
                 resultArr.push($(obj).attr('name')+"="+$(obj).val());  
               }
           }); 
           return resultArr;
        }
        
       
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }
   }     
})( jQuery );

$(document).ready(function(){
    $.sheeplaWidjetCreator();
});