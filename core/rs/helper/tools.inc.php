<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper;

use RS\Config\Loader;
use RS\Db\Adapter;
use RS\Exception;
use RS\Http\Request;
use RS\View\Engine;

/**
* Класс содержит вспомогательные функции для вызова из любого места кода.
*/
class Tools
{
    /**
    * Оборачивает каждый элемент массива одиночными кавычками + экранирует значения для вставки
    * 
    * @param array $arr - массив элементов
    * @param array $except_key - пропускать элементы по ключу
    * @param string $char - буква которой оборачивать значения
    * @param boolean $allow_null - пропускать элементы по ключу
    * @return array
    */
    public static function arrayQuote($arr, array $except_key = null, $char = "'", $allow_null = false)
    {
        foreach($arr as $k => &$v)
        {
            if ($except_key===null || !in_array($k, $except_key)) {
                if ($allow_null && $v === null) {
                    $v = 'NULL';
                } else {
                    $v = $char. Adapter::escape($v).$char;
                }
            }
        }
        return $arr;
    }

    /**
    * Подставляет нужную словоформу в зависимости от количественного признака
    * Например: 1 огурец, 2 огурца, 5 огурцов, 24 огурца,....
    * 
    * @param mixed $count - количество предметов
    * @param mixed $first - форма для 1-го предмета, например: (один)'огурец'
    * @param mixed $second - форма для 2-х предметов, например: (два)'огурца'
    * @param mixed $five - форма для 5-ти предметов, например: (пять)'огурцов'
    * @return string
    */
    public static function verb( $count, $first, $second, $five )
    {
        $prepare = abs( intval( $count ) );
        if( $prepare !== 0 ) 
        {
            if( ( $prepare - $prepare % 10 ) / 10 == 1 ) return $five;
            $prepare = $prepare % 10;
            if( $prepare == 1 ) return $first;
            if( $prepare > 1 && $prepare < 5 ) return $second;
            return $five;
        }
        else 
            return $five;
    }
    
    /**
    * @desc Делает из заданного текста тизер не более заданного размера в байтах
    * 
    * @param string $text Исходный текст
    * @param int $size Максимальный размер врзвращаемого значения в байтах
    * @param bool $strip_tags исходный и результирующий текст являются html
    * @returns string Строка содержащая обрезанный текст
    */
    static function teaser($text, $size, $strip_tags = false)
    {
        $str = (!$strip_tags) ? $text : strip_tags($text);
        if(mb_strlen($str)>$size)
        { 
            $str = mb_substr($str,0,$size); 
            if(preg_match('/(.*)[\,|\s|\.]/us',$str, $match)) 
            {
                $str = $match[1];
            }
            $str .= "...";
        }
        return $str;
    }    
    
    /**
    * Отправляет одно письмо на email
    * @deprecated Рекомендуется использовать \RS\Helper\Mailer, вместо данного метода
    * 
    * @param string $subject - Тема
    * @param string $from - От кого - текст
    * @param string $reply - Кому отвечать - текст
    * @param string $from_email - От кого - email
    * @param string $reply_email - Кому отвечать - email
    * @param string $email email - адресата
    * @param string $email_tpl - шаблон письма адресату
    * @param array $data - массив с переменными
    * @return bool
    */
    public static function sendEmail($subject, $from, $reply, $from_email, $reply_email, $email, $email_tpl, $data)
    {
        $subject = self::mime_header_encode($subject);
        
        $from = empty($from) ? $from_email : self::mime_header_encode($from).'<'.$from_email.'>';
        $replyto = empty($replyto) ? $reply_email : self::mime_header_encode($reply).'<'.$reply_email.'>';
        
        $mail_header="MIME-Version: 1.0"."\r\n".
                     "Content-type: text/html; charset=utf-8"."\r\n".
                     "From: $from"."\r\n".
                     "Reply-To: $replyto"."\r\n";
        
        $view = new Engine();
        $view->assign('data', $data);
        $content = $view->fetch($email_tpl);

        $result = true;
        if (!empty($email)) {
            $emails = explode(',', $email);
            foreach($emails as $one_email) {
                $result = $result && mail(trim($one_email), $subject, $content, $mail_header);
            }
        }
        return $result;
    }
    
    /**
    * @deprecated Рекомендуется использовать /RS/Helper/Mailer, вместо данного метода    
    */
    public static function sendEmailSimple($subject, $email, $email_tpl, $data)
    {
        $system_config = Loader::getSystemConfig();
        
        self::sendEmail(
            $subject, 
            $system_config->getNoticeFrom(false),
            $system_config->getNoticeReply(false),
            $system_config->getNoticeFrom(true),
            $system_config->getNoticeReply(true),
            $email,
            $email_tpl,
            $data
        );
    }
    
    /**
    * @deprecated Рекомендуется использовать /RS/Helper/Mailer, вместо данного метода    
    */
    public static function mime_header_encode($str, $data_charset = 'utf-8', $send_charset = 'utf-8') 
    {
      if($data_charset != $send_charset) {
        $str = iconv($data_charset, $send_charset, $str);
      }
      return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
    }
    
    /**
    * Генерирует пароль определенной длины
    * 
    * @param integer $len длина сгенерированной строки
    * @param array|string $symb допустимые символы для генерации
    * @return string
    */
    public static function generatePassword($len, $symb = null)
    {
        srand();
        if ($symb === null) {
            $symb = [];
            foreach (range('a', 'z') as $letter) $symb[] = $letter;
            foreach (range('A', 'Z') as $letter) $symb[] = $letter;
            foreach (range('0', '9') as $letter) $symb[] = $letter;
        }
        if (is_string($symb)) {
            $symb = str_split($symb);
        }
        
        $pass = '';   
        for ($i=0; $i<$len; $i++) {
            $pass .= $symb[rand(1, count($symb))-1];
        }
        return $pass;
    }
    
    /**
    * Переводит текущую ветку xml в объект SimpleXML
    * @param \XMLReader $xml
    * @return \SimpleXMLElement
    */
    function xml2simple(\XMLReader $xml)
    {
        $dom = new \DOMDocument();
        $dom->appendChild($xml->expand());
        $xml->next();
        return simplexml_import_dom($dom);
    }
    
    /**
    * Заменяет в строке format %k на слово "сегодня, или вчера или 2 дня назад, или дата если больше месяца назад"
    * %v на месяц на русском языке в род. падеже. например: мая, июня
    * 
    * @param mixed $format
    * @param mixed $timestamp
    * @return string format 
    */
    public static function dateExtend($format, $timestamp)
    {
        //%k будет заменяться на слово "сегодня, или вчера или 2 дня назад, или дата если больше месяца назад"
        if (strpos($format, '%k') !==false) {
            $tdate = date('j-n-Y', $timestamp);
            if ($tdate == date('j-n-Y')) $format = str_replace('%k', t('сегодня'), $format);
            elseif ($tdate == date('j-n-Y', strtotime('-1 day'))) $format = str_replace('%k', t('вчера'), $format);
            elseif ($timestamp > (time() - 60*60*24*31)) 
            {
                $diff = time()-$timestamp;
                $days = ceil($diff/(60*60*24));
                $format = str_replace('%k', $days." ". Tools::verb($days, t("день"), t("дня"), t("дней")).t(" назад"), $format);
            }
            else 
            {
                $format = str_replace('%k', date('d.m.Y', $timestamp), $format);
            }
        }

        if (strpos($format, '%dw') !== false) {
            $day_of_week = [t('Вс'), t('Пн'), t('Вт'), t('Ср'), t('Чт'), t('Пт'), t('Сб')];
            $format = str_replace('%dw', $day_of_week[date('w', $timestamp)], $format);
        }

        if (strpos($format, '%dW') !== false) {
            $day_of_week = [t('воскресенье'), t('понедельник'), t('вторник'), t('среда'), t('четверг'), t('пятница'), t('суббота')];
            $format = str_replace('%dW', $day_of_week[date('w', $timestamp)], $format);
        }
        
        //%v будет заменен на месяц на русском языке в род. падеже. например: мая, июня
        if (strpos($format, '%v') !== false) {
            $months = [1 => t('января'), t('февраля'), t('марта'), t('апреля'), t('мая'), t('июня'),
                            t('июля'), t('августа'), t('сентября'), t('октября'), t('ноября'), t('декабря')];
            $format = str_replace('%v', $months[date('n', $timestamp)], $format);
        }
        
        if (strpos($format, '%!Y') !== false) {
            //Отображает год, только если он не равен текущему
            $format = str_replace('%!Y', date('Y', $timestamp) == date('Y') ? '' : '%Y', $format);
        }
        
        if (strpos($format, '@date') !== false) {
            $format = str_replace('@date', '%d.%m.%Y', $format);
        }        
        if (strpos($format, '@time') !== false) {
            $format = str_replace('@time', '%H:%M', $format);
        }
        if (strpos($format, '@sec') !== false) {
            $format = str_replace('@sec', '%S', $format);
        }
        
        if (strpos($format, '%datetime') !== false) {
            $format = str_replace('%datetime', '%d.%m.%Y %H:%M:%S', $format);
        }
        
        return $format;
    }
    
    /**
    * Рекурсивно экранирует ключи и значения массива функцией htmlspecialchars
    * 
    * @param array $array исходный массив 
    * @return array возвращает экранированный исходный массив
    */
    public static function escapeArrayRecursive(array $array)
    {
        $result = [];
        foreach($array as $key => $value) {
            $key = htmlspecialchars($key);
            if (is_array($value)) {
                $result[$key] = self::escapeArrayRecursive($value);
            } else {
                $result[$key] = self::toEntityString($value);
            }
        }
        return $result;
    }
    
    /**
    * Переводит спецсимволы строки в entity
    * 
    * @param string $str
    * @return string
    */
    public static function toEntityString($str)
    {
        return htmlspecialchars($str);
    }
    
    /**
    * Переводит спецсимволы из entity в строку
    * 
    * @param string $str
    * @return string
    */
    public static function unEntityString($str)
    {
        return htmlspecialchars_decode($str);
    }
    
    /**
    * Проверяет, соответствует ли версия $version требуемой $need
    * 
    * @param string $need - требуемая версия, например 5.3 или 5.03.2525
    * @param string $version - имеющаяся версия, например 5.2.10
    * @param string $compare - указывает какой знак сравнения должен стоять между $version и $need
    * @throws Exception
    * @return bool Возвращает true, если версия $version больше или равно $need
    */
    public static function compareVersion($need, $version, $compare = '>=')
    {
        $need_parts = explode('.', $need);
        foreach($need_parts as &$part) {
            $part = sprintf('%05d', $part);
        }
        
        $parts = explode('.', $version);
        $ver_parts = [];
        for($i=0; $i<count($need_parts); $i++) {
            $one = isset($parts[$i]) ? $parts[$i] : 0;
            $ver_parts[] = sprintf('%05d', $one);
        }
        
        $need_str = implode('', $need_parts);
        $ver_str = implode('', $ver_parts);
        
        $cmp_result = strcmp($ver_str, $need_str);
        switch ($compare) {
            case ">": return $cmp_result > 0;
            case "<": return $cmp_result < 0;
            case ">=": return $cmp_result >= 0;
            case "<=": return $cmp_result <= 0;
            case "==": return $cmp_result == 0;
        }
        
        throw new Exception(t('Передан некорректный тип сравнения версий'));
    }    
    
    /**
    * Возвращает url, в случае, если он не содержит сторонних доменов.
    * Поддомены в URL разрешаются. В противном случае возвращается $error_url
    * 
    * @param string $url проверяемый адрес 
    * @param string $error_url адрес в случае ошибки
    * 
    * @return string
    */
    public static function cleanOpenRedirect($url, $error_url = '/')
    {
        $my_domain = Request::commonInstance()->server('HTTP_HOST');
        if (preg_match('/^(http)?(s)?(:)?\/\/([^\/?]+)/' , trim($url), $match) ) {
            //Если обнаружен домен в URL
            if (strpos($match[4], $my_domain) !== false) {
                return $url;
            }
            return $error_url;
        }
        return $url;
    }
    
    /**
    * Переделывает цену из цифр в строки
    * 
    * @param float $price - цена цифрами
    * @return string
    */
    public static function priceToString($price)
    {
       $price =  number_format($price, 2, '.', '');
       list($price, $kopeiki) = explode('.', $price);
       
       $cur = t('[plural:%0:рубль|рубля|рублей]', [$price]); //Плюрал по рублям
       
       # Все варианты написания чисел прописью от 0 до 999 скомпануем в один небольшой массив 
       $m= [
            [t('ноль')],
            explode(',',t('-,один,два,три,четыре,пять,шесть,семь,восемь,девять')), 
            explode(',',t('десять,одиннадцать,двенадцать,тринадцать,четырнадцать,пятнадцать,шестнадцать,семнадцать,восемнадцать,девятнадцать')), 
            explode(',',t('-,-,двадцать,тридцать,сорок,пятьдесят,шестьдесят,семьдесят,восемьдесят,девяносто')), 
            explode(',',t('-,сто,двести,триста,четыреста,пятьсот,шестьсот,семьсот,восемьсот,девятьсот')), 
            explode(',',t('-,одна,две'))
       ];
       # Все варианты написания разрядов прописью скомпануем в один небольшой массив 
       $r= [explode(',',t('...ллион,,а,ов')),
            // используется для всех неизвестно больших разрядов 
            explode(',',t('тысяч,а,и,')), 
            explode(',',t('миллион,,а,ов')), 
            explode(',',t('миллиард,,а,ов')), 
            explode(',',t('триллион,,а,ов')), 
            explode(',',t('квадриллион,,а,ов')), 
            explode(',',t('квинтиллион,,а,ов')) 
            // ,array(... список можно продолжить 
       ];
       if($price>0) {
           # Если число ноль, сразу сообщить об этом и выйти $o=array(); 
           # Сюда записываем все получаемые результаты преобразования 
           # Разложим исходное число на несколько трехзначных чисел и каждое полученное такое число обработаем отдельно 
           foreach(array_reverse(str_split(str_pad($price,ceil(strlen($price)/3)*3,'0',STR_PAD_LEFT),3))as$k=>$p){ 
               $o[$k]= [];
           # Алгоритм, преобразующий трехзначное число в строку прописью 
           foreach($n=str_split($p)as$kk=>$pp) 
              if(!$pp) continue;
              else 
                  switch($kk){ 
                  case 0:
                      $o[$k][]=$m[4][$pp];
                      break; 
                  case 1:
                     if($pp==1){
                          $o[$k][]=$m[2][$n[2]];
                          break 2;
                      }else
                      $o[$k][]=$m[3][$pp];
                      break; 
                   case 2:
                      if(($k==1)&&($pp<=2))
                          $o[$k][]=$m[5][$pp];
                      else
                         $o[$k][]=$m[1][$pp];
                      break; 
              }
              $p*=1;
              if(!$r[$k]) $r[$k]=reset($r);
               
              # Алгоритм, добавляющий разряд, учитывающий окончание руского языка 
              if($p&&$k)
                   switch(true) { 
                       case preg_match("/^[1]$|^\\d*[0,2-9][1]$/",$p):
                           $o[$k][]=$r[$k][0].$r[$k][1];
                           break; 
                       case preg_match("/^[2-4]$|\\d*[0,2-9][2-4]$/",$p):
                           $o[$k][]=$r[$k][0].$r[$k][2];
                           break; 
                       default:
                          $o[$k][]=$r[$k][0].$r[$k][3];
                       break; 
                   }$o[$k]=implode(' ',$o[$k]); 
              } 
              
          $string_price = implode(' ',array_reverse($o));
            
       }else{
          $string_price = $m[0][0];  
       }  
       $string_kopeiki = t('[plural:%0:копейка|копейки|копеек]', [(int)$kopeiki]);
           
       return  $string_price." ".$cur." ".$kopeiki." ".$string_kopeiki;
    }

    /**
     * Проверяет, соответствует ли версия $version условиям $version_expr
     *
     * @param string $version - проверяемая версия, например: 1.0.0
     * @param string $version_expr - строка с условиями проверки версий
     * Например: '0.1.0.0' (одна версия)
     * или '0.1.0.0 - 0.2.0.0'  (Диапазон версий)
     * или '>=0.1.0.156' или '<=0.1.0.200' (для всех версий младше или старше требуемой)
     * Можно указать смешанно, через запятую так: '<=0.1.0.200, 0.2.0.0 - 0.3.0.0, 1.0.0.0, 1.1.0.0'
     * @return bool Возвращает true, если $version соответствует хотя бы одному из условий $version_expr, иначе false.
     */
    public static function checkVersionRange($version, $version_expr)
    {
        foreach(explode(',', $version_expr) as $range) {
            $range = trim($range);
            if (preg_match('/^(.*?)-(.*?)$/', $range, $match)) {

                if (version_compare($version, trim($match[1])) > -1
                    && version_compare($version, trim($match[2])) < 1)
                    return true;

            } elseif (preg_match('/^\>\=(.*?)$/', $range, $match)) {

                if (version_compare($version, trim($match[1])) > -1)
                    return true;

            } elseif (preg_match('/^\<\=(.*?)$/', $range, $match)) {

                if (version_compare($version, trim($match[1])) < 1)
                    return true;

            } else {
                //Точный номер версии
                if (version_compare($version, $range) == 0)
                    return true;
            }
        }

        return false;
    }

    /**
     * Проверяет соответствует ли дата формату
     *
     * @param string $date - дата в виде строки (25.06.2018)
     * @param string $format - форматы даты для проверки
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Проверяет доступна ли страница\объект по url (например для проверки доступности изображения на удаленном сервере)
     *
     * @param string $url - абсолютный адрес
     * @return bool
     */
    public static function urlExists($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $headers = get_headers($url);
            return stripos($headers[0], "200 OK") ? true : false;
        }
        return false;
    }
}
