<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>发布任务</title>
    <script src="/public/common/js/jquery.min.js"></script>
    <script src="/public/common/js/jquery.cityselect.js"></script>
    <script src="/public/common/js/mdialog.1.0.min.js"></script>
    <script src="/public/bang/js/validate.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    <link type="text/css" rel="stylesheet" href="/public/bang/css/bang.css">
    <link type="text/css" rel="stylesheet" href="/public/common/css/frozen.css">
    <script>
        wx.config({
            debug: false,
            appId: '{$signPackage["appId"]}',
            timestamp: '{$signPackage["timestamp"]}',
            nonceStr: '{$signPackage["nonceStr"]}',
            signature: '{$signPackage["signature"]}',
            jsApiList: [
                // 所有要调用的 API 都要加到这个列表中
                'onMenuShareAppMessage', 'onMenuShareTimeline'
            ]
        });
        var canOperate = false;
        var host = 'http://' + window.location.host;
        var title = '爱帮忙，一起来帮忙！';
        var userId = '{$userInfo.id}';
        userId = userId ? userId : '{$loginUserInfo.id}';
        var link = host + '/Index.php/Bang';
        var shareLink = userId ? ( link + '/id/' + userId) : link;
        var imgUrl = host + '/public/bang/image/bang.jpg';
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
<body>
<div class="ui-tips ui-tips-info" style="margin-top: 3%;">
    <i></i><a href="tel:17749120968">当前系统正在测试优化中，欢迎使用！<br>我们期待您的反馈！<br>联系电话：<span
                style="color: orangered;">17749120968</span></a>
</div>
<div class="ui-form ui-border-t" style="margin-top: 3%;">

    <form id="info-form"
          action="/Bang/Index/publishsubmit"
          method="post">
        <div class="ui-form-item ui-border-b ui-form-item-show">
            <label>
                任务类别
            </label>

            <div class="ui-select">
                <select id="task_category" name="category">
                    {{foreach $task_categorys as $task_category}}
                    <option>{{$task_category}}</option>
                    {{/foreach}}
                </select>
            </div>
        </div>
        <div class="ui-form-item ui-border-b ui-form-item-show">
            <label>
                任务描述
            </label>
            <input type="text" name="instruction" placeholder="描述您的任务"/>
        </div>
        <div class="ui-form-item ui-border-b ui-form-item-show">
            <label>
                任务酬劳
            </label>
            <input type="text" name="reward" placeholder="如:1.01(单位:元)"/>
        </div>
        <div class="ui-form-item ui-border-b ui-form-item-show">
            <label>
                结束时间
            </label>

            <div class="ui-select-group">
                <div class="ui-select">
                    <select name="year_end" id="year_end">
                    </select>
                </div>

                <div class="ui-select" style="padding-left: 6%;">
                    <select name="month_end" id="month_end">
                    </select>
                </div>
                <div class="ui-select" style="padding-left: 6%;">
                    <select name="day_end" id="day_end">
                    </select>
                </div>
            </div>
        </div>
        <span id="city_select_start">
            <div class="ui-form-item ui-border-b ui-form-item-show">
                <label>
                    开始地址
                </label>

                <div class="ui-select">
                    <select class="prov" name="prov_start"></select>
                </div>
            </div>
            <div class="ui-form-item ui-border-b ui-form-item-show">
                <div class="ui-select">
                    <select class="city" name="city_start" disabled="disabled"></select>
                </div>
            </div>
            <div class="ui-form-item ui-border-b ui-form-item-show">
                <div class="ui-select">
                    <select class="dist" name="dist_start" disabled="disabled"></select>
                </div>
            </div>
            <div class="ui-form-item ui-border-b ui-form-item-show">
                <label style="margin-left: 2em;">
                    详细
                </label>

                <input type="text" name="area_detail_start" placeholder="详细地址"/>
            </div>
        </span>

        <span id="city_select_goal">
            <div class="ui-form-item ui-border-b ui-form-item-show">
                <label>
                    执行地址
                </label>

                <div class="ui-select">
                    <select name="prov_goal" class="prov"></select>
                </div>
            </div>
            <div class="ui-form-item ui-border-b ui-form-item-show">
                <div class="ui-select">
                    <select name="city_goal" class="city" disabled="disabled"></select>
                </div>
            </div>
            <div class="ui-form-item ui-border-b ui-form-item-show">
                <div class="ui-select">
                    <select name="dist_goal" class="dist" disabled="disabled"></select>
                </div>
            </div>
            <div class="ui-form-item ui-border-b ui-form-item-show">
                <label style="margin-left: 2em;">
                    详细
                </label>

                <input type="text" name="area_detail_goal" placeholder="详细地址"/>
            </div>
        </span>

        <div class="ui-form-item ui-border-b ui-form-item-show">
            <label>
                任务备注
            </label>
            <input type="text" name="comment" placeholder="仅自己可见（选填）"/>
        </div>
        <div class="ui-btn-group" style="margin:6% auto;">
            <a type="submit" style="margin: 0 auto;width: 92%;" class="ui-btn-lg ui-btn-primary" id="submit"
               value="提&nbsp&nbsp&nbsp&nbsp交">
                提&nbsp&nbsp&nbsp&nbsp交
            </a>
        </div>
    </form>
</div>
<script>
    ///时间选择部分控制
    function autoCreateDaySelect(fullYear, month) {
        var myDate = new Date();
        var daySelect = document.getElementById("day_end");
        daySelect.options.length = 0;
        //当月的天数
        var dayCount = new Date(fullYear, month, 0).getDate();
        var dayIndex = (parseInt(month) == (myDate.getMonth() + 1)) ? myDate.getDate() : 1;
        for (; dayIndex <= dayCount; dayIndex++) {
            var dayOption = document.createElement('option');
            dayOption.text = (dayIndex.toString() + "日");
            daySelect.add(dayOption, null);
        }
    }
    function autoCreateMonthSelect(fullYear) {
        var myDate = new Date();
        var monthSelect = document.getElementById("month_end");
        var yearSelect = document.getElementById("year_end");
        monthSelect.options.length = 0;
        var baseMonth = (parseInt(fullYear) == myDate.getFullYear()) ? myDate.getMonth() : 0;
        for (var monthIndex = 1; monthIndex <= 3; monthIndex++) {
            var option = document.createElement('option');
            option.text = (baseMonth + monthIndex + "月");
            monthSelect.add(option, null);
            if ((baseMonth + monthIndex) == 12 && yearSelect.length < 2) {
                yearOption = document.createElement('option');
                yearOption.text = (parseInt(fullYear)) + 1 + "年";
                yearSelect.add(yearOption, null);
                break;
            }
        }
    }
    var myDate = new Date();
    var yearSelect = document.getElementById("year_end");
    var daySelect = document.getElementById("day_end");
    var monthSelect = document.getElementById("month_end");
    var yearOption = document.createElement('option');
    yearOption.text = myDate.getFullYear() + "年";
    yearSelect.add(yearOption, null);
    yearSelect.onchange = function () {
        autoCreateMonthSelect((yearSelect.options[yearSelect.selectedIndex]).value.replace("年", ""));
        autoCreateDaySelect((yearSelect.options[yearSelect.selectedIndex]).value.replace("年", ""),
                (monthSelect.options[monthSelect.selectedIndex]).value.replace("月", ""));
    };
    monthSelect.onchange = function () {
        autoCreateDaySelect((yearSelect.options[yearSelect.selectedIndex]).value.replace("年", ""),
                (monthSelect.options[monthSelect.selectedIndex]).value.replace("月", ""));
    };
    autoCreateMonthSelect((yearSelect.options[yearSelect.selectedIndex]).value.replace("年", ""));
    autoCreateDaySelect((yearSelect.options[yearSelect.selectedIndex]).value.replace("年", ""),
            (monthSelect.options[monthSelect.selectedIndex]).value.replace("月", ""));
    ///
    taskCategory = document.getElementById("task_category");
    taskCategory.onchange = function () {
        switch ((this.options[this.selectedIndex]).value) {
            case "线上完成":
                document.getElementById("city_select_goal").style.display = "none";
                document.getElementById("city_select_start").style.display = "none";
                break;
            case "物流托运":
                document.getElementById("city_select_goal").style.display = "";
                document.getElementById("city_select_start").style.display = "";
                break;
            case "帮取火车票":
                document.getElementById("city_select_goal").style.display = "";
                document.getElementById("city_select_start").style.display = "none";
                break;
        }
    };
    document.getElementById("task_category").onchange();
    $("#city_select_start").citySelect({prov: "北京", city: "东城区"});
    $("#city_select_goal").citySelect({prov: "陕西", city: "咸阳", dist: "杨凌区"});
    function showDialog(str, func) {
        MDialog.open({
            title: "提示",
            content: str,
            onPositive: func
        });
    }
    //验证两位小数或只有一位小数
    function validate_decimal_two(value) {
        return (/^\d+(\.\d{2})?$/.test(value)) || (/^\d+(\.\d)?$/.test(value));
    }
    function validate_form(thisform) {
        with (thisform) {
            if (validate_required(instruction) == false) {
                showDialog("请描述您需要完成的任务", function () {
                    instruction.focus();
                });
                return false;
            }
            if ((validate_decimal_two(reward.value.replace('元', '')) == false)) {
                showDialog("请输入您愿意支付的酬劳", function () {
                    reward.focus();
                });
                return false;
            }
            if (reward.value.replace('元', '') < 1) {
                showDialog("您最少需要支付1元", function () {
                    reward.focus();
                });
                return false;
            }
            if (document.getElementById("city_select_start").style.display != "none") {
                if (validate_required(area_detail_start) == false) {
                    showDialog("请输入详细的出发地址！", function () {
                        area_detail_start.focus();
                    });
                    return false;
                }
            }
            if (document.getElementById("city_select_goal").style.display != "none") {
                if (validate_required(area_detail_goal) == false) {
                    showDialog("请输入详细的目的地！", function () {
                        area_detail_goal.focus();
                    });
                    return false;
                }
            }
            return true;
        }
    }
    pathName = window.location.pathname;
    scriptPath = 'http://' + window.location.host + ((pathName.indexOf('index.php/') != -1) ? '/index.php' : '');
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
                    switch (jsonObj.errCode) {
                        case 0:
                            MDialog.open({
                                showTitle: false,
                                content: "任务发布成功",
                                onPositive: function () {
                                    window.location.href = scriptPath + '/Bang/Index/index';
                                }
                            });
                            break;
                        case 2:
                            MDialog.open({
                                showTitle: false,
                                content: "<div style='text-align: center;'>您已是我们的用户</div><div style='text-align: center;'>无需重复注册！</div>",
                                contentType: "html",
                                onPositive: function () {
                                    window.location.href = scriptPath + '/Bang/Index/index';
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
            formData.append("category_index",
                    document.getElementById("task_category").selectedIndex);
            sendData(formData, infoForm.action);
        }
        return false;
    };
    ///删除运营商添加的广告
    adsNode = document.getElementsByClassName("_ih").item(0);
    if (adsNode)
        document.body.removeChild(adsNode);
    ///
</script>
</body>
</html>