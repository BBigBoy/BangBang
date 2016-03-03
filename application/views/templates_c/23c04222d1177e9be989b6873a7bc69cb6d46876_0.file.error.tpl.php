<?php
/* Smarty version 3.1.29, created on 2016-03-01 09:28:08
  from "/home/yafstudy/1/application/views/Bang/error/error.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_56d560a8622fb1_25002626',
  'file_dependency' => 
  array (
    '23c04222d1177e9be989b6873a7bc69cb6d46876' => 
    array (
      0 => '/home/yafstudy/1/application/views/Bang/error/error.tpl',
      1 => 1456741963,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_56d560a8622fb1_25002626 ($_smarty_tpl) {
?>
"Error Msg:"  . <?php ob_start();
echo $_smarty_tpl->tpl_vars['exception']->value->getMessage();
$_tmp1=ob_get_clean();
echo $_tmp1;?>
;
<?php }
}
