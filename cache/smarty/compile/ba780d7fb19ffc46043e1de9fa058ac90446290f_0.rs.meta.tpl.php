<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:54:42
  from "C:\OpenServer\domains\rieltprof.local\templates\system\meta.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf623ce791_84604116',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ba780d7fb19ffc46043e1de9fa058ac90446290f' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\system\\meta.tpl',
      1 => 1620406462,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf623ce791_84604116 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_replace')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\plugins\\modifier.replace.php';
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['meta_vars']->value, 'tagparam');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tagparam']->value) {
?>
<meta <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tagparam']->value, 'value', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['value']->value,'"','&quot;');?>
" <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>
>
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
}
}
