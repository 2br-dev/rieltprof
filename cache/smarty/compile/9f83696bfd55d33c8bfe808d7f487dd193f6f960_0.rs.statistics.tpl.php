<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:54:41
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\rieltprof\statistics.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf618eee30_86055322',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9f83696bfd55d33c8bfe808d7f487dd193f6f960' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\rieltprof\\statistics.tpl',
      1 => 1613769771,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf618eee30_86055322 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="registered-block">
    <?php $_smarty_tpl->_assignInScope('config_rieltprof', \RS\Config\Loader::ByModule('rieltprof'));
?>
    <div class="header">В системе зарегистрировано</div>
    <hr>
    <div class="registered-values">
        <div class="left">
            <div class="value"><?php echo $_smarty_tpl->tpl_vars['config_rieltprof']->value->getCountAllUsers();?>
</div>
            <div class="key"><?php echo $_smarty_tpl->tpl_vars['config_rieltprof']->value->num_word($_smarty_tpl->tpl_vars['config_rieltprof']->value->getCountAllUsers(),array('риэлтор','риэлтора','риэлторов'),false);?>
</div>
        </div>
        <div class="right">
            <div class="value"><?php echo $_smarty_tpl->tpl_vars['config_rieltprof']->value->getCountAllAds();?>
</div>
            <div class="key"><?php echo $_smarty_tpl->tpl_vars['config_rieltprof']->value->num_word($_smarty_tpl->tpl_vars['config_rieltprof']->value->getCountAllAds(),array('объект','объекта','объектов'),false);?>
</div>
        </div>
    </div>
</div>
<?php }
}
