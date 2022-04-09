/**
* Плагин, инициализирующий работу покупки корзины в один клик
*/
$.extend({
    oneClickCart: function( method ) {
        var defaults = {
            form   : '.oneClickCartForm',               //Селектор формы для отправки
            blockWrapper : '.oneClickCartWrapper',      //Селектор оборачивающей блок
            toggleOneClickCart : '#toggleOneClickCart', //Идентификатор оборачивающего блока
            openBlockFlag : false
        },
        $this = $('body'),
        args = arguments,
        data = $this.data('oneClickCart');
        
        if (!data) { //Инициализация
            data = { options: defaults };
            $this.data('oneClickCart', data);
        }
        
        var methods = {
            init: function(initoptions) {
                if (data) return;
                data                    = {}; $this.data('oneClickCart', data);
                data.options            = $.extend({}, defaults, initoptions);
                
                methods.bindChanges(); //Привяжем нужные события  
                $(window).on('cart.beforeupdate', beforeUpdateCart);                 
                $(window).on('cart.afterupdate', methods.bindChanges); //Привяжем после обновления корзины
            },
            
            /**
            * Переключает открытие или закрытие блока
            * 
            * @param boolean open - true - открыть, блок false - закрыть
            */
            openBlockTrigger: function(open){
                $(data.options.toggleOneClickCart, $this).toggle(open);
                data.options.openBlockFlag = open; //Запомним последнее состояние блока
                if (open){
                    $.colorbox.resize();
                }
            },
            
            /**
            *  Переключатель блока с формой оформления заказа в один клик
            * 
            */
            triggerBlockTrigger: function(){
                $(data.options.toggleOneClickCart, $this).toggle();
                var open = $(data.options.toggleOneClickCart, $this).is(":visible");
                data.options.openBlockFlag = open; //Запомним последнее состояние блока
                $.colorbox.resize();
            },
            
            /**
            * Возвращает флаг открытия или закрытия блока
            * 
            */
            getOpenBlockFlag: function(){
                return data.options.openBlockFlag;
            },
            
            /**
            * Навешивает необходимые события
            * 
            */
            bindChanges: function(){
                $(data.options.form, $this).off('submit.oneClickCart').on('submit.oneClickCart', methods.refresh); //Попытка отправки формы
                methods.openBlockTrigger(methods.getOpenBlockFlag()); //
            },
            
            /**
            * Обновление блока с отправкой в один клик
            * 
            */
            refresh: function(event){
                var params = $(this).serialize();  
                $.ajax({
                    type : 'POST',
                    url  : $(this).attr('action'),
                    data : params,
                    dataType : 'json',
                    success : function(response){
                        if (response.success){ //Если успех и мы в диалоговом окне
                            methods.openBlockTrigger(false);
                            //Обновим сведения по корзине
                            $.cart('refresh');
                            //Вызовем диалог
                            $.colorbox({
                                className : "noBorder",
                                html : response.html,
                                opacity: 0.4
                            });
                        }else{
                            var context = $(data.options.blockWrapper, $this); //Текущий контекст
                            var html = $(response.html);
                            //Заменим контент
                            context.html($(data.options.blockWrapper, html).html());
                            methods.openBlockTrigger(true);
                            methods.bindChanges(); //Навесим событие    
                        }  
                        
                        $this.trigger('oneclickcart.update');
                    }
                });
                event.preventDefault(); 
            }
         
        };        
        
        //private 
        /**
        * Функция срабатывает перед обновлением корзины и отключает поля ввода
        * 
        */
        var beforeUpdateCart = function(){
            $("input", $(data.options.form, $this)).prop('disabled', true);
        }
        
        
        
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, args );
        }
    }

});


$(function() {
    $.oneClickCart(); //Инициализируем плагин
});