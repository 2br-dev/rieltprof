<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:10
  from "C:\OpenServer\domains\rieltprof.local\modules\main\view\adminblocks\headerpanel\header_public_panel_items.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf7e1197a8_16654039',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cdb1227d4b25254d966bb5bb475b67d051058b87' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\modules\\main\\view\\adminblocks\\headerpanel\\header_public_panel_items.tpl',
      1 => 1620406462,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf7e1197a8_16654039 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
<li>
    <a <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value['attr'], 'value', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>
>
        <?php if ($_smarty_tpl->tpl_vars['item']->value['attr']['icon']) {?><i class="rs-icon rs-public-icon <?php echo $_smarty_tpl->tpl_vars['item']->value['attr']['icon'];?>
"></i><?php }?>
        <span><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</span>
    </a>
</li>
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
}
}
