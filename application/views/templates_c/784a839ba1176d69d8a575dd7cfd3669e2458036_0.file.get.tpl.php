<?php
/* Smarty version 3.1.29, created on 2016-03-02 14:39:16
  from "/home/yafstudy/1/application/views/Bang/index/get.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_56d6fb14d36642_44239667',
  'file_dependency' => 
  array (
    '784a839ba1176d69d8a575dd7cfd3669e2458036' => 
    array (
      0 => '/home/yafstudy/1/application/views/Bang/index/get.tpl',
      1 => 1456929330,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_56d6fb14d36642_44239667 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>领取任务</title>
    <link type="text/css" rel="stylesheet" href="/public/common/css/weui.min.css">
    <?php echo '<script'; ?>
 src="/public/common/js/jquery.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/public/common/js/mdialog.1.0.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
>
        wx.config({
            debug: false,
            appId: '<?php echo $_smarty_tpl->tpl_vars['signPackage']->value["appId"];?>
',
            timestamp: '<?php echo $_smarty_tpl->tpl_vars['signPackage']->value["timestamp"];?>
',
            nonceStr: '<?php echo $_smarty_tpl->tpl_vars['signPackage']->value["nonceStr"];?>
',
            signature: '<?php echo $_smarty_tpl->tpl_vars['signPackage']->value["signature"];?>
',
            jsApiList: [
                // 所有要调用的 API 都要加到这个列表中
                'onMenuShareAppMessage', 'onMenuShareTimeline'
            ]
        });
        var canOperate = false;
        var host = 'http://' + window.location.host;
        var title = '爱帮忙，一起来帮忙！';
        var userId = '<?php echo $_smarty_tpl->tpl_vars['userInfo']->value['id'];?>
';
        userId = userId ? userId : '<?php echo $_smarty_tpl->tpl_vars['loginUserInfo']->value['id'];?>
';
        var link = host + '/Bang';
        var shareLink = userId ? ( link + '/id/' + userId) : link;
        var imgUrl = host + '/Public/Common/Images/logoe.png';
        wx.ready(function () {
            canOperate = true;
            wx.onMenuShareAppMessage({
                title: title,
                desc: '爱帮忙，一起来帮忙！',
                link: shareLink,
                imgUrl: imgUrl,
                type: '',
                dataUrl: ''
            });

            wx.onMenuShareTimeline({
                title: title,
                desc: '爱帮忙，一起来帮忙！',
                link: shareLink,
                imgUrl: imgUrl,
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

        });
    <?php echo '</script'; ?>
>
</head>
<body ontouchstart="">

<div id="latest-list" class="weui_cells weui_cells_access" style="padding: 0;margin: 0;">
    <a class="weui_cell" href="javascript:;">
        <div class="weui_cell_hd weui_icon_warn"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p>&nbsp;&nbsp;&nbsp;&nbsp;当前任务系统尚未开放，敬请期待！</p>
        </div>
    </a>
    <?php
$_from = $_smarty_tpl->tpl_vars['tasks']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_task_0_saved_item = isset($_smarty_tpl->tpl_vars['task']) ? $_smarty_tpl->tpl_vars['task'] : false;
$_smarty_tpl->tpl_vars['task'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['task']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['task']->value) {
$_smarty_tpl->tpl_vars['task']->_loop = true;
$__foreach_task_0_saved_local_item = $_smarty_tpl->tpl_vars['task'];
?>
    <a class="weui_cell" href="javascript:;">
        <div class="weui_cell_hd"><img src="<?php echo $_smarty_tpl->tpl_vars['task']->value['headimgurl'];?>
" alt=""
                                       style="width:20px;margin-right:5px;display:block"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p><?php echo $_smarty_tpl->tpl_vars['task_categorys']->value[$_smarty_tpl->tpl_vars['task']->value['category']];?>
</p>
        </div>
        <div class="weui_cell_ft"><?php echo $_smarty_tpl->tpl_vars['task']->value['instruction'];?>
</div>
    </a>
    <?php
$_smarty_tpl->tpl_vars['task'] = $__foreach_task_0_saved_local_item;
}
if ($__foreach_task_0_saved_item) {
$_smarty_tpl->tpl_vars['task'] = $__foreach_task_0_saved_item;
}
?>
</div>
<?php echo '<script'; ?>
>
    $('.ui-list li').click(function () {
        if ($(this).data('href')) {
            location.href = $(this).data('href');
        }
    });
    ///删除运营商添加的广告
    adsNode = document.getElementsByClassName("_ih").item(0);
    if (adsNode)
        document.body.removeChild(adsNode);
    ///
<?php echo '</script'; ?>
>
</body>
</html><?php }
}
