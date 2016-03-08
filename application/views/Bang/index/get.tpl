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
    <script src="/public/common/js/jquery.min.js"></script>
    <script src="/public/common/js/mdialog.1.0.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        wx.config({
            debug: false,
            appId: '{{$signPackage["appId"]}}',
            timestamp: '{{$signPackage["timestamp"]}}',
            nonceStr: '{{$signPackage["nonceStr"]}}',
            signature: '{{$signPackage["signature"]}}',
            jsApiList: [
                // 所有要调用的 API 都要加到这个列表中
                'onMenuShareAppMessage', 'onMenuShareTimeline'
            ]
        });
        var canOperate = false;
        var host = 'http://' + window.location.host;
        var title = '爱帮忙，一起来帮忙！';
        var userId = '{{$userInfo.id}}';
        userId = userId ? userId : '{{$loginUserInfo.id}}';
        var link = host + '/index.php/Bang/Index/index';
        var shareLink = userId ? ( link + '/id/' + userId) : link;
        var imgUrl = host + '/public/bang/image/bangbang.jpg';
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
    </script>
</head>
<body ontouchstart="">

<div id="latest-list" class="weui_cells weui_cells_access" style="padding: 0;margin: 0;">
    <a class="weui_cell" href="javascript:;">
        <div class="weui_cell_hd weui_icon_warn"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p>&nbsp;&nbsp;&nbsp;&nbsp;当前任务系统尚未开放，敬请期待！</p>
        </div>
    </a>
    {{foreach $tasks as $task}}
    <a class="weui_cell" href="javascript:;">
        <div class="weui_cell_hd"><img src="{{$task.headimgurl}}" alt=""
                                       style="width:20px;margin-right:5px;display:block"></div>
        <div class="weui_cell_bd weui_cell_primary">
            <p>{{$task_categorys[$task['category']]}}</p>
        </div>
        <div class="weui_cell_ft">{{$task.instruction}}</div>
    </a>
    {{/foreach}}
</div>
<script>
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
</script>
</body>
</html>