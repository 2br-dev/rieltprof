<?php
  function smarty_modifier_teaser($text, $size, $html=false)
    {
        $str = (!$html) ? $text : strip_tags($text);
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

