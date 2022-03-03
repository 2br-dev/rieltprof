<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/  
namespace RS\Helper;
  
/**
* @desc класс для валидаторов форм
* Можно дополнять класс новыми функциями валидации
*/
class FieldChecker
  {
  	  protected $checker_err; //Здесь ввиде HTML
  	  protected $form_err= [];
  	  protected $arr_err= []; //Здесь ошибки будут в виде массива
  	  
  	  
  	  function Chk_empty($param, $errtext, $form=null)
  	  {
		if (empty($param)) $this->SetErr($errtext,$form);  	  	  
  	  	return $param;
	  }
	  

	  function Chk_pattern($param,$errtext, $form=null, $pattern)
	  {
	  	  if (!@preg_match($pattern,$param)) $this->SetErr($errtext,$form);
	  	  return $param;
	  }
  	  
  	  /**
  	  * @desc Проверяет, если значение -1, то ошибка. Удобно использовать в SELECT.
  	  */
  	  function Chk_noselect($param,$errtext,$form=null)
  	  {
  	  	  if (!empty($param) && $param==-1) $this->SetErr($errtext,$form);
	  	  return $param;
	  }
	  
	  function Chk_date($day, $month, $year,$errtext, $form=null)
	  {
	  	  if (@!checkdate($month, $day, $year)) $this->SetErr($errtext, $form);
	  	  return @date('Y-m-d',mktime(0,0,0,$month,$day,$year));
	  }
	  
	  function Chk_minmax($param, $min, $max, $errtext, $form=null)
	  {
	  	  if ($min !== false && $param < $min || ($max !== false && $param > $max) ) $this -> SetErr($errtext, $form);
	  	  return $param;
	  }
	  
	  function Chk_email($param, $errtext, $form=null)
	  {
          if (filter_var($param, FILTER_VALIDATE_EMAIL) === false) 
                $this->SetErr($errtext,$form);
          return $param;
	  }
  	  
 	  
   	  function SetErr($err,$form)
   	  {
  		 if (empty($this->checker_err)) $this->checker_err='';
  			$this->checker_err.='<p>'.$err;
  			$this->arr_err[]=$err;
  		 if (!empty($form))	$this->form_err[$form][] = $err;
	  }
      
      function ClearErr()
      {
          $this->checker_err = '';
          $this->arr_err = [];
          
      }
	  
	  function GetErr()
	  {
	  	 return $this->checker_err;  
	  }
	  
	  function GetErrArr()
	  {
	  	  return $this->arr_err;
	  }
  	  
  	  function GetFormErr()
  	  {
  	  	return $this->form_err;  	  	  
	  }
      
      function GetFormErrInline($form)
      {
          return isset($this->form_err[$form]) ? implode(', ', $this->form_err[$form]) : '';
      }
	  
	  function isErr()
	  {
	  	  if (!empty($this -> arr_err)) return true;
	  	  return false;
	  }
  	  
  }
  

