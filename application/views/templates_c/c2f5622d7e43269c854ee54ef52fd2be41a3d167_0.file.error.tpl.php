<?php
/* Smarty version 3.1.29, created on 2016-03-01 06:23:54
  from "/home/yafstudy/1/application/views/Index/error/error.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_56d5357ad94986_05332214',
  'file_dependency' => 
  array (
    'c2f5622d7e43269c854ee54ef52fd2be41a3d167' => 
    array (
      0 => '/home/yafstudy/1/application/views/Index/error/error.tpl',
      1 => 1456741963,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_56d5357ad94986_05332214 ($_smarty_tpl) {
?>
"Error Msg:"  . <?php echo $_smarty_tpl->tpl_vars['exception']->value->getMessage();?>
;
<?php }
}
