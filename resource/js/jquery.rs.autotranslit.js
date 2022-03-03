/**
* Plugin, активирующий автоматическую транслитерацию поля
* @author ReadyScript lab.
*/
(function($){
    $.fn.autoTranslit = function(method) {
        var defaults = {
            formAction: 'form[action]',
            context:'form, .virtual-form',
            virtualForm: '.virtual-form',
            addPredicate: '=add',
            targetName: null,
            showUpdateButton: true
        }, 
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('autoTranslit');
            
            var methods = {
                init: function(initoptions) {                    
                    if (data) return;
                    data = {}; $this.data('autoTranslit', data);
                    data.options = $.extend({}, defaults, initoptions);
                    if ($this.data('autotranslit')) {
                        data.options.targetName = $this.data('autotranslit');
                    }
                    data.options.target = $('input[name="'+data.options.targetName+'"]', $(this).closest(data.options.context));
                    if (data.options.target) {
                        //Подключаем автоматическую транслитерацию, если происходит создание объекта
                        var isAdd;
                        if ($this.closest(data.options.virtualForm).length) {
                            isAdd = $this.closest(data.options.virtualForm).data('isAdd');
                        } else {
                            isAdd = $this.closest(data.options.formAction).attr('action').indexOf(data.options.addPredicate) > -1;
                        }
                        if (isAdd) {
                            $this.on('blur', onBlur);
                        }
                        if (data.options.showUpdateButton) {
                            var update = $('<a class="update-translit"></a>').click(onUpdateTranslit).attr('title', lang.t('Транслитерировать заново'));
                            $(data.options.target).after(update).parent().trigger('new-content');
                        }
                    }
                }
            };
            
            //private 
            var onBlur = function() {
                if (data.options.target.val() == '') {
                    onUpdateTranslit();
                }
            },
            onUpdateTranslit = function() {
                data.options.target.val( translit( $this.val() ) );
            },
            translit = function( text ) {
                let diacritical_characters = ('Ä ä À à Á á Â â Ã ã Å å Ǎ ǎ Ą ą Ă ă Æ æ Ā ā Ç ç Ć ć Ĉ ĉ Č č Ď đ Đ ď ð È'+
                    ' è É é Ê ê Ë ë Ě ě Ę ę Ė ė Ē ē Ĝ ĝ Ģ ģ Ğ ğ Ĥ ĥ Ì ì Í í Î î Ï ï ı Ī ī Į į Ĵ ĵ Ķ ķ Ĺ ĺ Ļ ļ Ł ł Ľ ľ '+
                    'Ñ ñ Ń ń Ň ň Ņ ņ Ö ö Ò ò Ó ó Ô ô Õ õ Ő ő Ø ø Œ œ Ŕ ŕ Ř ř ẞ ß Ś ś Ŝ ŝ Ş ş Š š Ș ș Ť ť Ţ ţ Þ þ Ț ț Ü'+
                    ' ü Ù ù Ú ú Û û Ű ű Ũ ũ Ų ų Ů ů Ū ū Ŵ ŵ Ý ý Ÿ ÿ Ŷ ŷ Ź ź Ž ž Ż ż').split(' '),
                    transform_characters   = ('a a a a a a a a a a a a a a a a a a ae ae a a c c c c c c c c d d d d d'+
                    ' e e e e e e e e e e e e e e e e g g g g g g h h i i i i i i i i i i i i i j j k k l l l l l l l '+
                    'l n n n n n n n n o o o o o o o o o o o o o o oe oe r r r r s s s s s s s s s s s s t t t t th th'+
                    ' t t u u u u u u u u u u u u u u u u u u w w y y y y y y z z z z z z').split(' ');

                var rus = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о',
                    'п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я'].concat(diacritical_characters);

                var eng = ['a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k',
                    'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh',
                    'sch', '', 'y', '', 'e', 'yu', 'ya'].concat(transform_characters);

                var result = '', char;
                var hyphen = false;
                for(var i=0; i<text.length; i++) {
                    char = text.toLowerCase().charAt(i);

                    if (char.match(/[a-z0-9]/gi)) {
                        result = result + char;
                        hyphen = false;
                    } else {
                        var pos = rus.indexOf(char);
                        if (pos > -1) {
                            result = result + eng[pos];
                            hyphen = false;
                        } else if (!hyphen) {
                            result = result + '-';
                            hyphen = true;
                        }
                    }
                }

                //Вырезаем по краям знак минуса "-"
                result = result.replace(/^\-+|\-+$/g, '');

                return result;
            };
            
            
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

    $.contentReady(function() {
        $('input[data-autotranslit]', this).autoTranslit();
    });

})(jQuery);