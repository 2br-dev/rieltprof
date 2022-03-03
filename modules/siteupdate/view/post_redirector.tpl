<html>
<head>
<meta http-equiv="Content-type" content="text/html; Charset=utf-8" >
</head>
    <body>
        <form action="{$url}" method="POST">
           <input type="hidden" name="license" value="{$license}"/> 
           <input type="hidden" name="domain" value="{$domain_hash}"/>  
           <input type="submit" value="Продолжить" id="sub"/>
        </form>
        <script type="text/javascript">
           document.getElementById('sub').style.display = 'none';
           document.forms[0].submit();
        </script>        
    </body>
</html>