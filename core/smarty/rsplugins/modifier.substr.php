<?php
  function smarty_modifier_substr($text, $start, $length = null)
    {
        if ($length !== null) {
            return mb_substr($text, $start, $length);
        } else {
            return mb_substr($text, $start);
        }
    }  
