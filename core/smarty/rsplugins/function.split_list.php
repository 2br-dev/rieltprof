<?php
/**
* Разбивает массив на колонки, чтобы можно было удобно разместить данные в таблице
* Записывает результат в переменную указанную в параметре var
* 
* @param mixed $params
* @param mixed $smarty
* @return void
*/
function smarty_function_split_list($params, &$smarty)
{
    if (!isset($params['var'])) {
        trigger_error("split_list: param 'var' not found", E_USER_NOTICE);
        return;
    }

    if (!isset($params['item'])) {
        trigger_error("split_list: param 'item' not found", E_USER_NOTICE);
        return;
    }
    
    $mk = microtime(true);
    
    $smarty->assign($params['var'], []);
    
    // Устанавливаем количество колонок в зависимости от количества элементов в массиве
    $incols = isset($params['incols']) ? $params['incols'] : "0,11,21";
    $incols_arr = explode(',', $incols);
    $total = count($params['item']);    
    if (!$total) return;    
    
    //Определяем, как будем размещать значения: по строкам (горизонтально), по столбцам (вертикально)
    $is_horizontal = isset($params['horizontal']) ? true : false;
    
    if (count($incols_arr ) > 1) { //Режим - количество колонок зависит от количества элементов
        
        //Формируем правила, вкаком случае сколько колонок будет
        for($i=1, $max=count($incols_arr); $i<=$max; $i++) {
            $start = $incols_arr[$i-1];
            $end = ($i==$max) ? 0 : $incols_arr[$i]-1;
            $rules[$i] = [$start, $end];
        }
        
        $col_count = 0;
        
        //Определяем к какое правило соответствует нашему количеству элементов
        foreach($rules as $numcol => $rule) {
            if ($rule[0]<= $total) {
                if ($rule[1] == 0 || $total <= $rule[1]) {
                    $col_count = $numcol;
                    break;
                }
            }
        }
        
        if (!$col_count) return;
        
    } else {
        //Режим - фиксированное число колонок.
        $col_count = $incols;
    }
    
    $result = [];

    if ($is_horizontal) 
    {
        //Горизонтальная заливка данных
        $row = 0;
        $i = 0;
        foreach($params['item'] as $item) {
            if ($i % $col_count == 0) $row++;
            $result[$row][] = $item;
            $i++;
        }
    } else {
        //Вертикальная заливка данных
        $per_col = ceil($total/$col_count); //Элементов в колонке
        
        $cur_col = -1; //Текущая колонка
        $i = 0; //Текущая строка
        //Разбиваем по строкам, колонкам
        foreach($params['item'] as $item) {
            if ($i == 0) $cur_col++;
            $result[$i][$cur_col] = $item;
            if ($i < $per_col-1) $i++; else $i=0;
        }        
    }
    
    //Дописываем пустые колонки (чтобы таблица была правильная)
    foreach ($result as &$line) {
        if (count($line) < $col_count) {
            $line = array_merge($line, array_fill(0, $col_count-count($line), false) );
        }
    }
    
    $smarty->assign($params['var'], $result);
}
