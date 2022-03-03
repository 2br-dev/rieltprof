<?php
function smarty_function_urlmake($params, &$smarty)
{
    $escape = false;
    if (isset($params['escape']) && $params['escape']) {
       $escape = true; 
       unset($params['escape']);
    }
    
    $searchKeys = [];
    if (isset($params['__searchkey'])) 
    {
        explode(',', $params['__searchkey']);
        unset($params['__searchkey']);
    }      
    
    $prefix = '';
    if (isset($params['__prefix'])) {
         $prefix = $params['__prefix'];
         unset($params['__prefix']);
    }
    
    $key = null;
    $tmp = [];
    foreach ($params as $param=>$value)
    {
        if (preg_match('/^__key(.*)$/', $param)) {
            $key = $value; 
            unset($params[$param]);
        }
        
        if (isset($key) && preg_match('/^__val(.*)$/', $param))
        {
            $tmp[$key] = $value;
            unset($params[$param]);
            $key = null;
        }
    }
    
    $params = array_merge($params, $tmp);
    $url    = \RS\Http\Request::commonInstance()->replaceKey($params, $searchKeys, $prefix);
    if ($escape){ //если нужно заэкранировать
       $url = str_replace("&","&amp;", $url);
    }
    return $url;
}  

