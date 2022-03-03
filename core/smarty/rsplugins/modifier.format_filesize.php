<?php
  function smarty_modifier_format_filesize($inbytes)
    {
        return \RS\File\Tools::fileSizeToStr($inbytes);
    }  
