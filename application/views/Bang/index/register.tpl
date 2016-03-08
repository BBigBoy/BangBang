<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>注册爱帮忙</title>
    <link type="text/css" rel="stylesheet" href="/public/bang/css/register.css">
    <script src="/public/bang/js/validate.js"></script>
    <link type="text/css" rel="stylesheet" href="/public/common/css/frozen.css">
    <script src="/public/common/js/mdialog.1.0.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</head>
<body>
<div class="ui-form ui-border-t" style="margin-top: 3%;">
    <form id="info-form"
          action="/Bang/Index/registersubmit"
          method="post">
        <div class="ui-form-item ui-border-b ui-form-item-show">
            <label>
                姓名
            </label>
            <input type="text" name="name" placeholder="请输入姓名">
        </div>
        <div class="ui-form-item ui-border-b ui-form-item-show">
            <label>
                昵称
            </label>
            <input type="text" name="nickname" value="{{$smarty.session.nickname}}" placeholder="请输入昵称"/>
        </div>
        <div class="ui-form-item ui-border-b ui-form-item-show">
            <label>
                邮箱
            </label>
            <input type="email" name="email" placeholder="请输入邮箱"/>
        </div>
        <div class="ui-form-item ui-form-item-textarea ui-border-b ui-form-item-show">
            <label>
                个性签名
            </label>
            <textarea name="personalized_signature" placeholder="请输入个性签名"></textarea>
        </div>
        <div class="ui-form-item ui-form-item-l ui-border-b">
            <label class="ui-border-r">
                中国 +86
            </label>
            <input type="tel" name="tel" placeholder="请输入手机号码">
        </div>
        <!--<div class="ui-form-item ui-form-item-r ui-border-b">
            <input type="text" placeholder="请输入验证码">
            &lt;!&ndash; 若按钮不可点击则添加 disabled 类 &ndash;&gt;
            <button type="button" class="ui-border-l">重新发送</button>
            <a href="#" class="ui-icon-close"></a>
        </div>-->
        <div class="ui-btn-group" style="margin-top: 6%;">
            <a type="submit" style="margin: 0 auto;width: 92%;" class="ui-btn-lg ui-btn-primary" id="submit"
               value="提&nbsp&nbsp&nbsp&nbsp交">
                提&nbsp&nbsp&nbsp&nbsp交
            </a>
        </div>
    </form>
    <script>
        function showDialog(str, func) {
            MDialog.open({
                title: "提示",
                content: str,
                onPositive: func
            });
        }
        function validate_form(thisform) {
            with (thisform) {
                if (validate_required(name) == false) {
                    showDialog(name.getAttribute("placeholder"), function () {
                        name.focus();
                    });
                    return false;
                }
                if (validate_required(nickname) == false) {
                    showDialog(nickname.getAttribute("placeholder"), function () {
                        nickname.focus();
                    });
                    return false;
                }
                if (validate_email(email) == false) {
                    showDialog("请输入正确的邮箱地址", function () {
                        email.focus();
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
                        var jsonObj = eval("(" + jsonString + ")");
                        pathName = window.location.pathname;
                        switch (jsonObj.errCode) {
                            case 0:
                                MDialog.open({
                                    showTitle: false,
                                    content: "<div style='text-align: center;'>恭喜您</div><div style='text-align: center;'>注册已成功！</div>",
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
                                    content: "<div style='text-align: center;'>您已是我们的用户</div><div style='text-align: center;'>无需重复注册！</div>",
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
        }
        document.getElementById("submit").onclick = function () {
            infoForm = document.getElementById("info-form");
            validateResult = validate_form(infoForm);
            if (validateResult) {
                var FD = new FormData(infoForm);
                sendData(FD, infoForm.action);
            }
            return false;
        };
        ///删除运营商添加的广告
        adsNode = document.getElementsByClassName("_ih").item(0);
        if (adsNode)
            document.body.removeChild(adsNode);
        ///
    </script>
</div>
</body>
</html>