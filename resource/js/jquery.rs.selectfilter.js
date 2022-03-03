//Перегружаем поиск, чтобы искать регистронезависимо
$.expr[':'].containsIgnoreCase = function (n, i, m) {
    return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

/**
 * Плагин, активирует фильтр по мульти выбору из выпадающего списка
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.selectFilter = function( data ) {

        return this.each(function() {
            if ($(this).data('selectfilter')) return false;
            $(this).data('selectfilter', {});

            var defaults = {
               filterClass    : '.filter'      //Класс поля ввода фильтра
            };

            var options = $.extend({}, defaults, data);

            var wrapper = $(this);
            var searchBlock = $("select", wrapper); //Берем тот блок в котором надо смотреть
            $(options.filterClass, wrapper).on('keyup', function(){
                var term = $.trim($(this).val()); //Искомое слово
                if (term.length > 0){
                    searchWord(term);
                }else{
                    $("option, optgroup", searchBlock).show();
                }
            });

            //private
            /**
             * Ищет в списке слова и если находит, то оставляет это значение в списке с путем дородителя
             *
             * @param {string} term - слово по которому нужно фильтровать
             */
            var searchWord = function(term){
                $("option:visible, optgroup:visible", searchBlock).hide();
                var options = $("option:containsIgnoreCase(" + term + ")", searchBlock);
                //Пройдемся по списку и откроем, то что подходит
                options.each(function(){
                    var parent = $(this).parent();
                    showTree($(this), parent);
                });
            },
            /**
             * Показ родителя
             *
             * @param child - ребенок для которого нужно искать родителя
             * @param parent - объект родителя
             */
            showTree = function(child, parent){
                var level  = child.data('level');
                var index  = child.index();
                child.show();
                var next_level = level-1;
                if (next_level == -1){
                    return;
                }

                var closest_levels = $("option[data-level='" + next_level + "']", parent);

                var minimum_level = 0;
                closest_levels.each(function(){
                    var parent_index = $(this).index();
                    if ((parent_index < index) && (minimum_level < parent_index)){
                        minimum_level = parent_index;
                    }
                });
                var next_child = $("option:eq(" + minimum_level + ")", parent);
                if (next_child.css('display') !== 'none'){
                    return;
                }
                showTree(next_child, parent);
            }
        }); //each

    };

    $.contentReady(function() {
        $('.selectFilterWrapper').selectFilter();
    });

})( jQuery );