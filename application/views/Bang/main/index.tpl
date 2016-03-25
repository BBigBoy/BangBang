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
    <div class="tab-content" title="首页">
        <div class="tab-content1">
            {{foreach $tasks as $task}}
            <div class="msg" taskId="{{$task._id}}">
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
                        <span>{{$task.reward}}元</span>
                    </div>
                    <div>
                        <span>评论:</span>
                        <span>0</span>
                    </div>
                    <div>
                        {{if $task.status eq 0}}
                        未接
                        {{else}}
                        已接
                        {{/if}}
                    </div>
                </div>
                <div class="item-guide">›</div>
            </div>
            {{/foreach}}
        </div>
    </div>
    <div class="tab-content" title="发现">
        <div class="tab-content2">
            <div class="func-group">
                <div class="func-row  tab-content2-func-row-height">
                    <div class="func-item" style="background-image: url('/public/bang/image/express-delivery.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">取快递</div>
                    </div>
                    <div class="func-item" style="background-image: url('/public/bang/image/get-takeout.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">取外卖</div>
                    </div>
                </div>
                <div class="func-row  tab-content2-func-row-height">
                    <div class="func-item" style="background-image: url('/public/bang/image/driving-companion.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">找陪驾</div>
                    </div>
                    <div class="func-item" style="background-image: url('/public/bang/image/ppt.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">做PPT</div>
                    </div>
                </div>
                <div class="tab-content3-below"></div>
            </div>
        </div>
    </div>
    <div class="tab-content" title="个人">
        <div class="tab-content3">
            <img class="tab-content3-img" src="/public/bang/image/top-aibangmang.jpg">
            <div class="tab-content3-block func-group">
                <div class="func-row">
                    <div class="func-item" style="background-image: url('/public/bang/image/my-publish.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">我的发布</div>
                    </div>
                    <div class="func-item" style="background-image: url('/public/bang/image/my-income.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">我的收益</div>
                    </div>
                </div>
                <div class="func-row">
                    <div class="func-item" style="background-image: url('/public/bang/image/task-serch.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">任务查询</div>
                    </div>
                    <div class="func-item" style="background-image: url('/public/bang/image/my-message.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">我的留言</div>
                    </div>
                </div>
                <div class="func-row">
                    <div class="func-item" style="background-image: url('/public/bang/image/online-customer.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">优质服务</div>
                    </div>
                    <div class="func-item" style="background-image: url('/public/bang/image/service-explanation.png');">
                        <div class="func-item-top"></div>
                        <div class="func-item-bottom">服务说明</div>
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
        //获取url中的参数
        function getUrlParam(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg);  //匹配目标参数
            if (r != null) return unescape(r[2]);
            return null; //返回参数值
        }
        function editTitle(title) {
            document.title = title;
            var $body = $('body');
            var $iframe = $('<iframe style="width: 0;height: 0;" src="/favicon.ico"></iframe>').on('load', function () {
                setTimeout(function () {
                    $iframe.off('load').remove()
                }, 0);
            }).appendTo($body);
        }
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
                                        editTitle("爱帮忙-" + $(tabContents[j]).attr('title'));
                                        $(tabs[j]).children().each(
                                                function () {
                                                    $(this).addClass("blow-text-select");
                                                });
                                    }
                                    if (j == 0) {
                                        $("img.lazy").trigger("loadimg");
                                    }
                                }
                            });
                        }
                );
            }
            tabs[0].click();
            var host = 'http://' + window.location.host;
            var link = host + '/index.php/Bang/Main/taskDetail';
            $(".msg").on('click', function () {
                window.location.href = link + '?taskId=' + $(this).attr('taskId') +
                        (getUrlParam('ii') ? '&ii=99' : '');
            });
            $("img.lazy").lazyload({
                event: "loadimg"
            });//定义图片懒加载事件
            setTimeout(function () {
                $("img.lazy").trigger("loadimg");
            }, 3000);
        }
    </script>
</body>
</html>