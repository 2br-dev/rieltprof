<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:04
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\index.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf78e6fb91_53623839',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9831a7649944b0cd202f5acb05e1375978dd68a9' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\index.tpl',
      1 => 1615376343,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%rieltprof%/sidebar.tpl' => 1,
    'rs:%rieltprof%/add-object-menu.tpl' => 1,
    'rs:%rieltprof%/search-object-menu.tpl' => 1,
    'rs:%rieltprof%/statusbar.tpl' => 1,
    'rs:%rieltprof%/form/add-contact.tpl' => 1,
  ),
),false)) {
function content_621faf78e6fb91_53623839 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_function_addjs')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.addjs.php';
if (!is_callable('smarty_function_moduleinsert')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.moduleinsert.php';
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>

<?php $_smarty_tpl->_assignInScope('rieltprof_config', \RS\Config\Loader::ByModule('rieltprof'));
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_660225481621faf78e6e684_07452639', "content");
?>

<?php }
/* {block "content"} */
class Block_660225481621faf78e6e684_07452639 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <?php if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['enable_favorite']) {?>
        <?php echo smarty_function_addjs(array('file'=>"rs.favorite.js"),$_smarty_tpl);?>

    <?php }?>
    <div class="global-wrapper" id="main">
        <?php $_smarty_tpl->_subTemplateRender("rs:%rieltprof%/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <div class="content">
            <div class="top-block">
                <div class="row">
                    <div class="col">
                        <?php $_smarty_tpl->_subTemplateRender("rs:%rieltprof%/add-object-menu.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('referer'=>'/'), 0, false);
?>

                        <?php $_smarty_tpl->_subTemplateRender("rs:%rieltprof%/search-object-menu.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

                    </div>
                    <div class="col right-align force">
                        <a href="/blacklist/" class="btn" id="check-contact"><span>Проверить контакт</span></a>
                        <a href="" class="btn modal-trigger" data-target-modal="abuse-contact"><span>Внести контакт</span></a>
                    </div>
                    <div href="" class="burger" data-target="profile-sidebar">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </div>
                </div>
            </div>
            <div class="main-block">
                <h1>Последние добавленные объекты</h1>
                <?php echo smarty_function_moduleinsert(array('name'=>"\Rieltprof\Controller\Block\Allads"),$_smarty_tpl,'C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\index.tpl');?>

            </div>
        </div>
        <?php $_smarty_tpl->_subTemplateRender("rs:%rieltprof%/statusbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->_subTemplateRender("rs:%rieltprof%/form/add-contact.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    </div>








































<?php
}
}
/* {/block "content"} */
}
