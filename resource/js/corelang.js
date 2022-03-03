/**
* Функции для работы с интернационализацией строк ReadyScript в JavaScript
*
* @author ReadyScript lab.
*/
var lang = 
{
    baseLang: (global.baseLang) ? global.baseLang : null, //Язык по умолчанию
    lang: (global.lang) ? global.lang : null, //Текущий язык
    messages: {}, //Список фраз
    plugins: {},    
    /**
    * Возвращает перевод фразы на текущем языке
    * 
    * @param string phrase - фраза для перевода
    * @param object params - объект для подстановки значений
    * @param alias - короткий идентификатор фразы
    * @example 
    * lang.t('У вас %msg сообщений', {msg: 35})
    * lang.t('У вас %msg сообщений. Далее большой текст', {msg: 35}, 'короткий_идентификатор_фразы')
    */
    t: function(phrase, params, alias) {
        var self = this;
        var to_translate = (typeof(alias) == 'undefined') ? phrase : alias;
        var translated = this.messages[to_translate];
        if (translated) {
            phrase = translated;
        }

        phrase = phrase.replace(/\[(.*?):%(.*?):(.*?)\]/g, function(whole, plugin, param_name, plugin_param) {
            if (self.plugins[plugin]) {
                var param_value = (params[param_name]) ? params[param_name] : null;
                var phrase_lang = (translated) ? self.lang : self.baseLang;
                return self.plugins[plugin].call(self, param_value, plugin_param, params, phrase_lang);
            }
            return '';
        })
        
        for(var key in params) {
            phrase = this.str_replace('%'+key, params[key], phrase);
        }
        
        phrase = phrase.split('^')[0];
        return phrase;
    },
    /**
    * Replaces all occurrences of search in haystack with replace  
    * version: 1109.2015
    * discuss at: http://phpjs.org/functions/str_replace    
    * 
    * @param search
    * @param replace
    * @param subject
    * @param count
    */
    str_replace: function(search, replace, subject, count) {
        
            j = 0,
            temp = '',
            repl = '',
            sl = 0,        fl = 0,
            f = [].concat(search),
            r = [].concat(replace),
            s = subject,
            ra = Object.prototype.toString.call(r) === '[object Array]',        
            sa = Object.prototype.toString.call(s) === '[object Array]';
        s = [].concat(s);
        if (count) {
            this.window[count] = 0;
        } 
        for (i = 0, sl = s.length; i < sl; i++) {
            if (s[i] === '') {
                continue;
            }        for (j = 0, fl = f.length; j < fl; j++) {
                temp = s[i] + '';
                repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
                s[i] = (temp).split(f[j]).join(repl);
                if (count && s[i] !== temp) {
                    this.window[count] += (temp.length - s[i].length) / f[j].length;
                }
            }
        }
        return sa ? s : s[0];
    }
};

/**
* Плагин plural - подставляет слово во множественном числе с учетом словоформы.
* Пример испльзования во фразе: 
* lang.t("У вас %msg [plural:%msg:сообщение|сообщения|сообщений]", {msg: 1}); 
* lang.t("У вас %msg [plural:%msg:сообщение|сообщения|сообщений]", {msg: 2});
* lang.t("У вас %msg [plural:%msg:сообщение|сообщения|сообщений]", {msg: 5});
* lang.t("You have %msg [plural:%msg:message|messages]", {msg: 1}); //Если текущий язык английский
* lang.t("You have %msg [plural:%msg:message|messages]", {msg: 2}); //Если текущий язык английский
* 
* вывод:
* У Вас 1 сообщение
* У Вас 2 сообщения
* У Вас 5 сообщений
* You have 1 message
* You have 2 messages
* 
* @type Object
*/
lang.plugins.plural = function(param_value, plugin_param, params, phrase_lang) {
    var values = plugin_param.split('|');
    if (phrase_lang == 'ru') {
        var result;
        var first = values[0];
        var second = values[1];
        var five = values[2];
        
        var prepare = Math.abs( parseInt( param_value ) );
        if( prepare != 0 ) 
        {
            if( ( prepare - prepare % 10 ) / 10 == 1 ) {
                result = five;
            } else {
                prepare = prepare % 10;
                if( prepare == 1 ) {
                    result = first;
                } else if( prepare > 1 && prepare < 5 ) {
                    result = second;
                } else {
                    result = five;
                }
            }
        }
        else {
            result = five;
        }
    } else if (lang == 'en') {
        result = (param_value == 1) ? $values[0] : $values[1];
    } else {
        result = values[0];
    }
    return result;
};