<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:05
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\rieltprof\rating_info.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf791aa0e1_73292223',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4d236f4ef40081ce5705d550819da20fcc61a4e2' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\rieltprof\\rating_info.tpl',
      1 => 1613768018,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf791aa0e1_73292223 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="rating_info">
    <div class="rating_user" data-initial="<?php echo sprintf("%.1f",$_smarty_tpl->tpl_vars['user']->value['rating']);?>
" title="<?php echo sprintf("%.1f",$_smarty_tpl->tpl_vars['user']->value['rating']);?>
"></div>
    <p><?php echo $_smarty_tpl->tpl_vars['config']->value->num_word($_smarty_tpl->tpl_vars['user']->value->getCountReviews(),array('отзыв','отзыва','отзывов'));?>
</p>
</div>
<?php }
}
