<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:54:42
  from "C:\OpenServer\domains\rieltprof.local\templates\system\html.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf62129e82_21583296',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4e3770a6bd30417e63ac5dcee5409a661a7ffe3d' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\system\\html.tpl',
      1 => 1620406462,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf62129e82_21583296 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE <?php echo $_smarty_tpl->tpl_vars['app']->value->getDoctype();?>
>
<html <?php echo $_smarty_tpl->tpl_vars['app']->value->getHtmlAttrLine();?>
 <?php if ($_smarty_tpl->tpl_vars['SITE']->value['language']) {?>lang="<?php echo $_smarty_tpl->tpl_vars['SITE']->value['language'];?>
"<?php }?>>
<head <?php echo $_smarty_tpl->tpl_vars['app']->value->getHeadAttributes(true);?>
>
<title><?php echo $_smarty_tpl->tpl_vars['app']->value->title->get();?>
</title>
<?php echo $_smarty_tpl->tpl_vars['app']->value->meta->get();?>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getCss(), 'css');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['css']->value) {
echo $_smarty_tpl->tpl_vars['css']->value['params']['before'];?>
<link <?php if ($_smarty_tpl->tpl_vars['css']->value['params']['type'] !== false) {?>type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['css']->value['params']['type'])===null||$tmp==='' ? "text/css" : $tmp);?>
"<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['css']->value['file'];?>
" <?php if ($_smarty_tpl->tpl_vars['css']->value['params']['media'] !== false) {?>media="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['css']->value['params']['media'])===null||$tmp==='' ? "all" : $tmp);?>
"<?php }?> rel="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['css']->value['params']['rel'])===null||$tmp==='' ? "stylesheet" : $tmp);?>
"<?php if ($_smarty_tpl->tpl_vars['css']->value['params']['as']) {?> as="<?php echo $_smarty_tpl->tpl_vars['css']->value['params']['as'];?>
"<?php }
if ($_smarty_tpl->tpl_vars['css']->value['params']['crossorigin']) {?> crossorigin="<?php echo $_smarty_tpl->tpl_vars['css']->value['params']['crossorigin'];?>
"<?php }?>><?php echo $_smarty_tpl->tpl_vars['css']->value['params']['after'];?>

<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

<?php echo '<script'; ?>
>
    var global = <?php echo $_smarty_tpl->tpl_vars['app']->value->getJsonJsVars();?>
;
<?php echo '</script'; ?>
>
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getJs(), 'js');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['js']->value) {
echo $_smarty_tpl->tpl_vars['js']->value['params']['before'];
echo '<script'; ?>
 type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['js']->value['params']['type'])===null||$tmp==='' ? "text/javascript" : $tmp);?>
" src="<?php echo $_smarty_tpl->tpl_vars['js']->value['file'];?>
"<?php if ($_smarty_tpl->tpl_vars['js']->value['params']['async']) {?> async<?php }
if ($_smarty_tpl->tpl_vars['js']->value['params']['defer']) {?> defer<?php }?>><?php echo '</script'; ?>
><?php echo $_smarty_tpl->tpl_vars['js']->value['params']['after'];?>

<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

<?php if (!empty($_smarty_tpl->tpl_vars['app']->value->getJsCode('header'))) {
echo '<script'; ?>
 type="text/javascript"><?php echo $_smarty_tpl->tpl_vars['app']->value->getJsCode('header');
echo '</script'; ?>
>
<?php }
echo $_smarty_tpl->tpl_vars['app']->value->getAnyHeadData();?>

</head>
<body <?php if ($_smarty_tpl->tpl_vars['app']->value->getBodyClass() != '') {?>class="<?php echo $_smarty_tpl->tpl_vars['app']->value->getBodyClass();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['app']->value->getBodyAttrLine();?>
>
    <?php echo $_smarty_tpl->tpl_vars['body']->value;?>

    
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getCss('footer'), 'css');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['css']->value) {
?>
    <?php echo $_smarty_tpl->tpl_vars['css']->value['params']['before'];?>
<link <?php if ($_smarty_tpl->tpl_vars['css']->value['params']['type'] !== false) {?>type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['css']->value['params']['type'])===null||$tmp==='' ? "text/css" : $tmp);?>
"<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['css']->value['file'];?>
" <?php if ($_smarty_tpl->tpl_vars['css']->value['params']['media'] !== false) {?>media="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['css']->value['params']['media'])===null||$tmp==='' ? "all" : $tmp);?>
"<?php }?> rel="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['css']->value['params']['rel'])===null||$tmp==='' ? "stylesheet" : $tmp);?>
"><?php echo $_smarty_tpl->tpl_vars['css']->value['params']['after'];?>

    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

    
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getJs('footer'), 'js');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['js']->value) {
?>

    <?php echo $_smarty_tpl->tpl_vars['js']->value['params']['before'];
echo '<script'; ?>
 type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['js']->value['params']['type'])===null||$tmp==='' ? "text/javascript" : $tmp);?>
" src="<?php echo $_smarty_tpl->tpl_vars['js']->value['file'];?>
"<?php if ($_smarty_tpl->tpl_vars['js']->value['params']['async']) {?> async<?php } else { ?> defer<?php }?>><?php echo '</script'; ?>
><?php echo $_smarty_tpl->tpl_vars['js']->value['params']['after'];?>

    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

    <?php if (!empty($_smarty_tpl->tpl_vars['app']->value->getJsCode('footer'))) {?>
        <?php echo '<script'; ?>
 type="text/javascript"><?php echo $_smarty_tpl->tpl_vars['app']->value->getJsCode('footer');
echo '</script'; ?>
>
    <?php }?>
</body>
</html><?php }
}
