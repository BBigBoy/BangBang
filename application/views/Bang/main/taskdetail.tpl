<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>任务详情</title>
    <link type="text/css" rel="stylesheet" href="/public/bang/css/taskdetail.css">
    <script src="/public/common/js/jquery.min.js"></script>
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
        var link = host + '/index.php/Bang/Main/index';
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
<div class="msg" msgId="{{$task._id}}">
    <div class="msg-head">
        <img class="msg-head-img" src="{{$task.headimgurl}}">
        <div class="msg-head-text">
            <div class="msg-head-name">{{$task.nickname}}</div>
            <div class="msg-head-info">
                <span>西农大北校区</span>
            </div>
        </div>
    </div>
    <div class="msg-content">
        <span>{{$task.instruction}}</span>
    </div>
    <div class="msg-bottom">
        <div>
            <span>金额:</span>
            <span>{{$task.reward}}元</span>
        </div>
        <div>
            <span>任务发布时间:</span>
            <span>{{formatTime($task.time_start)}}</span>
        </div>
        <div>
            <span>任务最后领取时间:</span>
            <span>{{formatTime($task.time_end)}}</span>
        </div>
    </div>
</div>
<button class="btn"
        {{($task_status)?'disabled="disabled"':''}}>{{$task_status_text}}</button>
<a class="btn" href="tel:{{$task.tel}}">联系TA</a>
<div style="height: 500px;border: 2px dashed #e68;margin: 2px;">
    评论区:待做
</div>

<script>
</script>
</body>
</html>