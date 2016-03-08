<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>帮取火车票-爱帮忙</title>
    <link type="text/css" rel="stylesheet" href="/public/common/css/weui.min.css">
    <script src="/public/bang/js/validate.js"></script>
    <script src="/public/common/js/mdialog.1.0.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
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
        var link = host + '/index.php/Bang/Index/huochepiaotuanwei';
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
    <style>
        html, body {
            width: 100%;
        }

        p {
            text-indent: 2em;
        }

        .weui_btn {
            margin: 10px 10% !important;
        }

    </style>
</head>
<body>
{{if $userId }}
<span id="had-reg"></span>
{{/if}}
<img src="/public/bang/image/top-tuanwei.jpg" style="width: 100%;border-bottom: #ee7800 1px dashed;">
<!--<h1 style="text-align: center;color: #ee7800;">帮取火车票</h1>-->
<div style="margin: 3% 6px;border: #ee7800 1px dashed;padding: 5px;">
    <!--<p>“爱帮忙”是西农大学生创业团队倾心打造的，为同学提供便捷生活服务的平台。</p>

    <p>在这里，你可以通过发布一个任务来找人帮你完成一些“麻烦”的事情，当然您也可以在这里领取任务，来帮助他人。</p>-->

    <p>假期即将来临，小伙伴们既要专心复习，也心心恋着要早早回家。然而回家路途遥远，有太多的事情需要准备，是否会感觉分身乏术？</p>

    <p>是否担心取票时长长的队伍，以至于赶不上回家的火车？</p>

    <p>在宿舍与校门道路间拖着归家时大件的行李，寒风刺骨，“路途漫漫”，是否也会需要温暖的小车接送？</p>

    <p>其实这些现在都不需要你操心了，“爱帮忙”帮你搞定。“爱帮忙”现在为您提供免费取火车票服务，只需点击下方按钮，即可获得免费资格！</p>

    <p>“爱帮忙”还帮您联系了很多提供接送服务的司机 ，您只需要拨打他们的电话即可享受宿舍到火车站的接送服务！</p>

    <p>小伙伴们还等待什么，赶快转发朋友圈让大家看到这个福利吧！</p>
</div>
<a href="/Bang/Index/huochepiao"
   had-use="{{$huochepiao_had_use}}"
   id="huochepiao" class="weui_btn weui_btn_primary">
    免费帮取火车票</a>
<a href="/Bang/Index/jiaoche"
   class="weui_btn weui_btn_plain_primary">预约叫车</a>
<script>
    function sendData(data, url, dealFunc) {
        var XHR = new XMLHttpRequest();
        // 定义数据成功发送并返回后执行的操作
        XHR.onreadystatechange = function (ext) {
            if (ext.target.readyState == 4) {
                if (ext.target.status == 200) {
                    var jsonString = ext.target.response;
                    if (!jsonString) {
                        return;
                    }
                    isSubmiting = false;
//                    //alert(jsonString);
                    var jsonObj = eval("(" + jsonString + ")");
                    pathName = window.location.pathname;
                    dealFunc(jsonObj.errCode);
                }
            }
        };
        // 定义发生错误时执行的操作
        XHR.addEventListener('error', function (event) {
            showDialog("请稍后再试");
        });
        // 设置请求地址和方法
        XHR.open('POST', url, true);
        // 最后,发送我们的数据.
        XHR.send(data);
        isSubmiting = true;
    }
    document.getElementById('huochepiao').onclick = function () {
        if (this.getAttribute("had-use") == 'yes') {
            window.location.href = "/Bang/Index/msg";
            return false;
        }
        if (document.getElementById('had-reg')) {
            sendData(null, "/Bang/Index/huochepiaogetstraight", function (errCode) {
                switch (errCode) {
                    case 0:
                        window.location.href = "/Bang/Index/msg";
                        break;
                    case 2:
                        window.location.href = "/Bang/Index/msg";
                        break;
                    case -3:
                        window.location.reload();
                        break;
                }
            });
            return false;
        }
    };
</script>
</body>
</html>