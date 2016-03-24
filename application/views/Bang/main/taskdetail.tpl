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
<div class="msg" taskId="{{$task._id}}">
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
            <span>发布时间:</span>
            <span>{{formatTime($task.time_start)}}</span>
        </div>
        <div>
            <span>最后领取时间:</span>
            <span>{{formatTime($task.time_end)}}</span>
        </div>
    </div>
</div>
<button class="btn-lg"
        {{($task_status)?'disabled="disabled"':''}}>{{$task_status_text}}</button>
<a class="btn-lg" href="tel:{{$task.tel}}">联系TA</a>
<div class="divider"></div>
<div class="comment">
    <label>
        <textarea id="comment-input" parent-comment="0" class="comment-textarea"></textarea>
        <button class="btn-primary comment-btn-position" id="post-comment">评论</button>
        <div style="clear: both;"></div>
    </label>
    <div class="posted-comment" style="display: none;">
        <div class="msg-head">
            <img class="msg-head-img">
            <div class="msg-head-text">
                <div class="msg-head-name"></div>
            </div>
        </div>
        <div class="comment-content">
        </div>
        <div class="post-time">
        </div>
        <div class="reply-btn">◛</div>
    </div>
</div>
<div id="none-comment" class="none-comment">
    还没有朋友评论<br>快来抢楼吧!
</div>
<script>
    function getCookie(name) {
        var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
        if (arr != null) return unescape(arr[2]);
        return null;
    }
    function getDateDiff(time) {
        var publishTime = time,
                d_seconds,
                d_minutes,
                d_hours,
                d_days,
                timeNow = parseInt(new Date().getTime() / 1000),
                d,
                date = new Date(publishTime * 1000),
                Y = date.getFullYear(),
                M = date.getMonth() + 1,
                D = date.getDate(),
                H = date.getHours(),
                m = date.getMinutes(),
                s = date.getSeconds();
        //小于10的在前面补0
        if (M < 10) {
            M = '0' + M;
        }
        if (D < 10) {
            D = '0' + D;
        }
        if (H < 10) {
            H = '0' + H;
        }
        if (m < 10) {
            m = '0' + m;
        }
        if (s < 10) {
            s = '0' + s;
        }

        d = timeNow - publishTime;
        d_days = parseInt(d / 86400);
        d_hours = parseInt(d / 3600);
        d_minutes = parseInt(d / 60);
        d_seconds = parseInt(d);

        if (d_days > 0 && d_days < 3) {
            return d_days + '天前';
        } else if (d_days <= 0 && d_hours > 0) {
            return d_hours + '小时前';
        } else if (d_hours <= 0 && d_minutes > 0) {
            return d_minutes + '分钟前';
        } else if (d_seconds < 60) {
            if (d_seconds <= 0) {
                return '刚刚发表';
            } else {
                return d_seconds + '秒前';
            }
        } else if (d_days >= 3 && d_days < 30) {
            return M + '-' + D + '&nbsp;' + H + ':' + m;
        } else if (d_days >= 30) {
            return Y + '-' + M + '-' + D + '&nbsp;' + H + ':' + m;
        }
    }
    var commentCOM = $('.posted-comment:eq(0)');
    function commentInputGetFocus() {
        var commentInput = $('#comment-input');
        var parentId = $($(this).parent()).attr('id')
        commentInput.attr('parent-comment', parentId ? parentId.split('-')[1] : 0);
        $('body').animate({scrollTop:(commentInput.offset().top-60+'px')});
        commentInput.focus();
        return false;
    }
    function createComment(commentObj) {
        var comment = $(commentCOM.clone());
        comment.css('display', 'block');
        comment.attr('id', 'comment-' + commentObj['_id']);
        comment.find('img').attr('src', commentObj['headimgurl']);
        comment.find('div.msg-head-name').text(commentObj['nickname']);
        comment.find('div.comment-content').text(commentObj['content']);
        comment.find('div.post-time').text(getDateDiff(commentObj['post_time']));
        comment.find('div.reply-btn:eq(0)').on('click', commentInputGetFocus);
        if (commentObj['aim_comment'] != 0) {
            comment.css('padding-left', '30px');
            comment.css('background', '#eee');
            comment.find('div.comment-content').css('font-size', '0.9em');
            $('#comment-' + commentObj['aim_comment']).after(comment.get());
        } else {
            commentCOM.before(comment.get());
        }
    }
    function generateCommentArea(commentArr) {
        for (var i = 0; i < commentArr.length; i++) {
            createComment(commentArr[i]);
        }
        if (commentArr.length == 0) {
            $('#none-comment').css('display', 'block');
        }
    }
    window.onload = function () {
        var commentArr ={{$comments}};
        generateCommentArea(commentArr);
        $('.reply-btn').on('click', commentInputGetFocus);
        function showDialog(str, func) {
            MDialog.open({
                title: "提示",
                content: str,
                onPositive: func
            });
        }

        var isSubmiting = false;
        $('#post-comment').on('click', function () {
            comment = $('#comment-input').val();
            if (!comment) {
                showDialog("请输入评论!");
                return false;
            }
            var formData = new FormData();
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
            formData.append("aim_comment", $('#comment-input').attr('parent-comment'));
            formData.append("comment", comment);
            formData.append("task", $('.msg:eq(0)').attr('taskId'));
            var host = 'http://' + window.location.host;
            var sendUrl = host + '/index.php/Bang/Main/postcomment?ii=99';

            function sendData(data, url, successFunc) {
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
                            var jsonObj = eval("(" + jsonString + ")");
                            successFunc(jsonObj);
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

            sendData(formData, sendUrl, function (jsonObj) {
                //alert(jsonObj.errCode);
                switch (jsonObj.errCode) {
                    case 0:
                        MDialog.open({
                            showTitle: false,
                            content: "评论发布成功",
                            onPositive: function () {
                                $('#comment-input').attr('parent-comment', 0);
                                $('#comment-input').val('');
                                var comment = eval("(" + jsonObj.data + ")");
                                comment['headimgurl'] = getCookie('headimgurl');
                                comment['nickname'] = getCookie('nickname');
                                createComment(comment);
                            }
                        });
                        break;
                    case -3:
                        window.location.reload();
                        break;
                }
            });
            return false;
        });
    };
</script>
</body>
</html>