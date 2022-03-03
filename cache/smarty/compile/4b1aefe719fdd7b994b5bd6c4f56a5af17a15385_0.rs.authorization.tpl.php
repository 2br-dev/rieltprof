<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:54:41
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\users\authorization.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf6176a729_19871217',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4b1aefe719fdd7b994b5bd6c4f56a5af17a15385' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\users\\authorization.tpl',
      1 => 1615400827,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%rieltprof%/statistics.tpl' => 1,
  ),
),false)) {
function content_621faf6176a729_19871217 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_block_t')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\block.t.php';
if (!is_callable('smarty_function_moduleinsert')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.moduleinsert.php';
?>

<div class="authorization">
    <div class="auth-form-wrapper">
        <form method="POST" action="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('users-front-auth');?>
" id="login" class="modal-custom">
            <?php echo $_smarty_tpl->tpl_vars['this_controller']->value->myBlockIdInput();?>

            <input type="hidden" name="referer" value="/">
            <div class="modal-content">
                <div class="left"></div>
                <div class="right">
                    <div class="header">
                        <div class="left">
                            <img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/exclusives.svg" alt="">
                        </div>
                        <div class="right">
                            <img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/logo.svg" alt="">
                        </div>
                    </div>
                    <div class="text-data">
                        <div class="row header-holder">
                            <strong>Авторизация</strong>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="input-field">
                                    <input
                                            type="text"
                                            name="login"
                                            value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['login'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['Setup']->value['DEFAULT_DEMO_LOGIN'] : $tmp);?>
"
                                    ><label for="">Логин</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-field">
                                    <input type="password" name="pass" value="<?php echo $_smarty_tpl->tpl_vars['Setup']->value['DEFAULT_DEMO_PASS'];?>
"><label for="">Пароль</label>
                                </div>
                            </div>
                        </div>
                        <div class="row-fix">
                            <div class="col">
                                <input type="checkbox" id="remember" name="remember" value="1" <?php if ($_smarty_tpl->tpl_vars['data']->value['remember']) {?>checked<?php }?>> <label for="remember">Запомнить меня</label>
                            </div>
                            <div class="col right-align">
                                <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('users-front-auth',array("Act"=>"recover"));?>
" class="rs-in-dialog"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Забыли пароль?<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a>
                            </div>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="left">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('users-front-register');?>
" class="btn btn-outlined waves-effect waves-dark"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Заявка на регистрацию<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</a>
                        </div>
                        <div class="right">
                            <input type="submit" value="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Войти<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" class="btn waves-effect waves-light">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="error">
                <?php if (!empty($_smarty_tpl->tpl_vars['status_message']->value)) {?><div class="pageError"><?php echo $_smarty_tpl->tpl_vars['status_message']->value;?>
</div><?php }?>

                <?php if (empty($_smarty_tpl->tpl_vars['errors']->value) && $_smarty_tpl->tpl_vars['current_user']->value->hasError()) {?>
                    <?php $_smarty_tpl->_assignInScope('errors', $_smarty_tpl->tpl_vars['current_user']->value->getErrorsStr());
?>
                <?php }?>
                <?php if (!empty($_smarty_tpl->tpl_vars['error']->value)) {?>
                    <div class="error">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/attention.png" alt="Ошибка авторизации">
                        <div class="error_message">
                            <p>Ошибка авторизации</p>
                            <p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</p>
                        </div>

                    </div>
                <?php }?>
            </div>
        </form>
    </div>
    <?php $_smarty_tpl->_subTemplateRender("rs:%rieltprof%/statistics.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <?php echo smarty_function_moduleinsert(array('name'=>"Rieltprof\Controller\Block\Partners"),$_smarty_tpl,'C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\users\authorization.tpl');?>

</div>



































<?php }
}
