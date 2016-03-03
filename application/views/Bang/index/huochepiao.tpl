<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>帮取火车票-爱帮忙</title>
    <link type="text/css" rel="stylesheet" href="/public/common/css/frozen.css">
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
        userId = userId ? userId : '{{$loginUserInfo.id}}}';
        var link = host + '/Bang';
        var shareLink = userId ? ( link + '/id/' + userId) : link;
        var imgUrl = host + '/Public/bang/Images/bang.jpg';
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
    </style>
</head>
<body>
<form id="info-form" style="margin: 12% 6px;padding: 5px;border: #18b4ed 1px dashed;"
      action="/Bang/Index/huochepiaoget"
      method="post">
    <div class="ui-form-item ui-border-b ui-form-item-show">
        <label>
            姓名
        </label>
        <input type="text" name="name" placeholder="请输入姓名">
    </div>
    <div class="ui-form-item ui-form-item-l ui-border-b">
        <label class="ui-border-r">
            中国 +86
        </label>
        <input type="tel" name="tel" placeholder="请输入手机号码">
    </div>
    <div class="ui-btn-group" style="margin-top: 6%;">
        <a type="submit" style="margin: 0 auto;width: 92%;" class="ui-btn-lg ui-btn-primary" id="submit"
           value="一键获得免费资格">
            一键获得免费资格
        </a>
    </div>
</form>

<script>
    /*pathName = window.location.pathname;
     scriptPath = 'http://' + window.location.host + pathName.substr(0, pathName.indexOf('.php/') + 4);*/
    /*document.getElementById('get-free').onclick = function () {
     MDialog.open({
     showTitle: false,
     content: "<div style='text-align: center;'>亲喜欢我们的服务吗，</div><div style='text-align: center;'>" +
     "如果喜欢，您可以点击右上角，将这个消息分享给大家哦！</div>",
     contentType: "html",
     onPositive: function () {
     window.location.href = scriptPath + '/bang/Index/huochepiaoget';
     }
     });
     return false;

     };*/
    function showDialog(str, func) {
        MDialog.open({
            title: "提示",
            content: str,
            onPositive: func
        });
    }
    var isSubmiting = false;
    function sendData(data, url) {
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
                    //alert(jsonString);
                    var jsonObj = eval("(" + jsonString + ")");
                    pathName = window.location.pathname;
                    switch (jsonObj.errCode) {
                        case 0:
                            MDialog.open({
                                showTitle: false,
                                content: "<div style='text-align: center;'>恭喜您</div><div style='text-align: center;'>您可享受免费代取火车票服务！</div>",
                                contentType: "html",
                                onPositive: function () {
                                    window.location.href =
                                            'http://' + window.location.host +
                                            pathName.substr(0, pathName.indexOf('.php/') + 4) + '/bang/Index/index';
                                }
                            });
                            break;
                        case 2:
                            MDialog.open({
                                showTitle: false,
                                content: "<div style='text-align: center;'>您已申请本服务，</div><div style='text-align: center;'>无需重复申请！</div>",
                                contentType: "html",
                                onPositive: function () {
                                    window.location.href =
                                            'http://' + window.location.host +
                                            pathName.substr(0, pathName.indexOf('.php/') + 4) + '/bang/Index/index';
                                }
                            });
                            break;
                        case -3:
                            window.location.reload();
                            break;
                    }
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
    function validate_form(thisform) {
        with (thisform) {
            if (validate_required(name) == false) {
                showDialog(name.getAttribute("placeholder"), function () {
                    name.focus();
                });
                return false;
            }
            if (validate_mobile(tel) == false) {
                showDialog("请输入正确的手机号码", function () {
                    tel.focus();
                });
                return false;
            }
            return true;
        }
    }
    document.getElementById("submit").onclick = function () {
        infoForm = document.getElementById("info-form");
        validateResult = validate_form(infoForm);
        if (validateResult) {
            if (isSubmiting) {
                MDialog.open({
                    title: "提示",
                    content: "任务提交中，请耐心等待"
                });
                return false;
            }
            isSubmiting = true;
            setTimeout(function () {
                isSubmiting = false;
            }, 10000);
            var formData = new FormData(infoForm);
            sendData(formData, infoForm.action);
        }
        return false;
    };
</script>
</body>
</html>