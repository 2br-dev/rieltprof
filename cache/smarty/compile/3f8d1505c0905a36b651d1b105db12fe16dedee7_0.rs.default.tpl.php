<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:54:41
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\default.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf61eda136_22351811',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3f8d1505c0905a36b651d1b105db12fe16dedee7' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\default.tpl',
      1 => 1604730916,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf61eda136_22351811 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1718250791621faf61ed9816_57563928', "content");
?>

<?php }
/* {block "content"} */
class Block_1718250791621faf61ed9816_57563928 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>



        <?php echo $_smarty_tpl->tpl_vars['app']->value->blocks->getMainContent();?>


<?php
}
}
/* {/block "content"} */
}
