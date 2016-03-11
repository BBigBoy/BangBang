<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>爱帮忙</title>
    <script src="/public/common/js/jquery.min.js"></script>
    <script src="/public/common/js/jquery.lazyload.min.js"></script>
    <script src="/public/common/js/mdialog.1.0.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    <link type="text/css" rel="stylesheet" href="/public/bang/css/main.css">
    <link type="text/css" rel="stylesheet" href="/public/common/css/frozen.css">
</head>
<body ontouchstart="">
<div id="content" class="content">
    <div class="tab-content">
        <div class="tab-content1">
            {{foreach $tasks as $task}}
            <div class="msg">
                <div class="msg-head">
                    <img class="msg-head-img lazy" src="/public/bang/image/bangbang.jpg"
                         data-original="{{$task.headimgurl}}">
                    <div class="msg-head-text">
                        <div class="msg-head-name">{{$task.nickname}}</div>
                        <div class="msg-head-info">
                            <span>西农大北校区</span>
                            &nbsp;&nbsp;
                            <span>7分钟前</span>
                        </div>
                    </div>
                </div>
                <div class="msg-content">
                    <span>{{$task.instruction}}</span>
                </div>
                <div class="msg-bottom">
                    <div>
                        <span>金额:</span>
                        <span>8元</span>
                    </div>
                    <div>
                        <span>评论</span>
                        <span>1</span>
                    </div>
                    <div>
                        未接
                    </div>
                </div>
            </div>
            {{/foreach}}
            <div style="height: 43px;"></div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-content2">
            <div class="category-item">我的发布</div>
            <div class="category-item">我的收益</div>
            <div class="category-item">我的发布</div>
            <div class="category-item">我的收益</div>
            <div class="category-item">我的发布</div>
            <div class="category-item">我的收益</div>
            <div class="category-item">我的发布</div>
            <div class="category-item">我的收益</div>
        </div>
        <div style="height: 43px;"></div>
    </div>
    <div class="tab-content">
        <div class="tab-content3">
            <img class="tab-content3-img" src="/public/bang/image/top-aibangmang.jpg">
            <div class="self-func-group">
                <div class="self-func-row">
                    <div class="self-func-item">我的发布</div>
                    <div class="self-func-item">我的收益</div>
                </div>
                <div class="self-func-row">
                    <div class="self-func-item">任务查询</div>
                    <div class="self-func-item">我的留言</div>
                </div>
                <div class="self-func-row">
                    <div class="self-func-item">在线客户</div>
                    <div class="self-func-item">服务说明</div>
                </div>
            </div>
            <div class="tab-content3-below"></div>
        </div>
    </div>
</div>
<div class="footer">
    <div id="tab-group" class="tab-group">
        <div class="tab">
            <span class="ui-icon-home blow-text"></span>
            <div class="blow-text">首页</div>
        </div>
        <div class="tab">
            <span class="ui-icon-scan blow-text"></span>
            <div class="blow-text">发现</div>
        </div>
        <div class="tab">
            <span class="ui-icon-personal blow-text"></span>
            <div class="blow-text">个人</div>
        </div>
    </div>
</div>
<script>
    window.onload = function () {
        var tabs = $("#tab-group").children();
        var tabContents = $("#content").children();
        for (var i = 0; i < tabs.length; ++i) {
            $(tabs[i]).each(function () {
                        $(this).on("click", function () {
                            for (var j = 0; j < tabs.length; ++j) {
                                if (tabs[j] != this) {
                                    $(tabContents[j]).css("display", "none");
                                    $(tabs[j]).children().each(
                                            function () {
                                                $(this).removeClass("blow-text-select");
                                            });
                                } else {
                                    $(tabContents[j]).css("display", "block");
                                    $(tabs[j]).children().each(
                                            function () {
                                                $(this).addClass("blow-text-select");
                                            });
                                }
                                if (j == 0) {
                                    $("img.lazy").trigger("sporty");
                                }
                            }
                        });
                    }
            );
        }
        tabs[1].click();
        $("img.lazy").lazyload({
            event: "sporty"
        });//定义图片懒加载事件
        setTimeout(function () {
            $("img.lazy").trigger("sporty");
        }, 3000);
    }
</script>
</body>
</html>