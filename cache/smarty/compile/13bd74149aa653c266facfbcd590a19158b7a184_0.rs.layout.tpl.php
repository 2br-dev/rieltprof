<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:54:41
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\layout.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf61da3a68_37473792',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '13bd74149aa653c266facfbcd590a19158b7a184' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\layout.tpl',
      1 => 1630261196,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf61da3a68_37473792 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_function_addmeta')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.addmeta.php';
if (!is_callable('smarty_function_addcss')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.addcss.php';
if (!is_callable('smarty_function_addjs')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.addjs.php';
if (!is_callable('smarty_function_tryinclude')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.tryinclude.php';
?>

<?php echo smarty_function_addmeta(array('http-equiv'=>"X-UA-Compatible",'content'=>"IE=Edge",'unshift'=>true),$_smarty_tpl);
echo smarty_function_addmeta(array('name'=>"viewport",'content'=>"width=device-width, initial-scale=1.0"),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['enable_page_fade']) {
echo $_smarty_tpl->tpl_vars['app']->value->setBodyAttr(array('style'=>'opacity:0','onload'=>"setTimeout('document.body.style.opacity = &quot;1&quot;', 0)"));
}
echo smarty_function_addcss(array('file'=>"libs/magnific-popup.css"),$_smarty_tpl);
echo smarty_function_addcss(array('file'=>"common/lightgallery/css/lightgallery.min.css",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addcss(array('file'=>"%users%/verification.css"),$_smarty_tpl);
echo smarty_function_addcss(array('file'=>"https://unpkg.com/swiper/swiper-bundle.min.css",'basepath'=>"root"),$_smarty_tpl);
echo smarty_function_addcss(array('file'=>"master.css"),$_smarty_tpl);
echo smarty_function_addcss(array('file'=>"custom_styles.css"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"libs/jquery.min.js",'name'=>"jquery",'unshift'=>true,'header'=>true),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"libs/bootstrap.min.js",'name'=>"bootstrap"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"jquery.form/jquery.form.js",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"jquery.cookie/jquery.cookie.js",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"libs/jquery.sticky.js"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"libs/jquery.magnific-popup.min.js"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"maskedinput.js"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"lightgallery/lightgallery-all.min.js",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"%users%/verification.js"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"lab/lab.min.js",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"rs.profile.js"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"rs.changeoffer.js"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"rs.indialog.js"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"rs.cart.js"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"rs.theme.js"),$_smarty_tpl);
if ($_smarty_tpl->tpl_vars['shop_config']->value === false) {
echo $_smarty_tpl->tpl_vars['app']->value->setBodyClass('shopBase',true);
} else {
echo $_smarty_tpl->tpl_vars['app']->value->setBodyClass('noShopBase',true);
}
echo $_smarty_tpl->tpl_vars['app']->value->blocks->renderLayout();?>



<?php echo smarty_function_tryinclude(array('file'=>"%THEME%/scripts.tpl"),$_smarty_tpl);?>

<?php }
}
