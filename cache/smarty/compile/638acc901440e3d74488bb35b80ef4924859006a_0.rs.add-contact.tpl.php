<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:09
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\rieltprof\form\add-contact.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf7dac3821_19191138',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '638acc901440e3d74488bb35b80ef4924859006a' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\rieltprof\\form\\add-contact.tpl',
      1 => 1615380957,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf7dac3821_19191138 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="modal" id="abuse-contact">
    <div class="title">
        <span>
            Внести контакт
        </span>
        <div class="close-wrapper">
            <a href="" class="close"></a>
        </div>
    </div>
    <form class="modal-body" id="add-contact-form" method="POST" data-url="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('rieltprof-front-blacklist',array('Act'=>'addContact'));?>
">
        <div class="input-field">
            <input type="text" class="styled phone_mask" name="phone">
            <label>Номер телефона</label>
            <span class="review_error error-phone"></span>
            <span class="review_error error-denied"></span>
        </div>
        <div class="input-field">
            <textarea name="comment" id="" cols="30" rows="10" class="styled"></textarea>
            <label>Комментарий</label>
            <span class="review_error error-comment"></span>
        </div>
        <div class="right-align">
            <a href="" class="btn" id="add-contact">Отправить</a>
        </div>
    </form>
</div>
<div class="shadow"></div>
<?php }
}
