<html>
<head>
<meta http-equiv="Content-type" content="text/html; Charset=utf-8" >
</head>
    <body>
        {$payment_type=$transaction->getPayment()->getTypeObject()}
        <form action="{$url}" method="POST">
           {foreach $payment_type->getPostParams() as $param_key=>$param}
               {if is_array($param)}
                  {foreach $param as $key=>$sub_param}
                     <input type="hidden" name="{$param_key}[{$key}]" value='{$sub_param}'/> 
                  {/foreach}
               {else}
                  <input type="hidden" name="{$param_key}" value='{$param}'/>  
               {/if}
           {/foreach} 
           <input type="submit" value="{t}Продолжить{/t}" id="sub"/>
        </form>
        <script type="text/javascript">
           document.getElementById('sub').style.display = 'none';
           document.forms[0].submit();
        </script>
    </body>
</html>

