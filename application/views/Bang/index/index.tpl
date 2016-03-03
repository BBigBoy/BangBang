<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>爱帮忙</title>
    <script src="/public/common/js/jquery.min.js"></script>
    <script src="/public/common/js/mdialog.1.0.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    <link type="text/css" rel="stylesheet" href="/public/bang/css/bang.css">
    <link type="text/css" rel="stylesheet" href="/public/common/css/weui.min.css">
    <link type="text/css" rel="stylesheet" href="/public/common/plugin/owl-carousel/owl.carousel.css">
    <link type="text/css" rel="stylesheet" href="/public/common/plugin/owl-carousel/owl.theme.css">
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
    </script>
</head>
<body class="HolyGrail" ontouchstart="">
<notempty name="Think.session.userId">
    <span id="had-reg"></span>
</notempty>
<header>
    <div id="carousel-example-generic" class="owl-carousel owl-theme" data-ride="carousel"
         style="width:100%;height: 100%;border-bottom: #000000 1px dashed;">
        <div class="item"><img src="/public/bang/image/top-shangxian.jpg" alt="shang xian"></div>
        <div class="item"><a href="/Bang/Index/huochepiaotuanwei">
            <img src="/public/bang/image/top-huochepiao.jpg" alt="The Last of us"></a></div>
        <div class="item"><a href="/Bang/Index/jiaoche">
            <img src="/public/bang/image/top-zhuche.jpg" alt="zhu che"></a></div>
        <div class="item"><img src="/public/bang/image/top-aibangmang.jpg" alt="GTA V"></div>
    </div>
</header>
<div class="HolyGrail-body">
    <a href="javascript:;" id="get-task" class="weui_btn weui_btn_primary">领取任务</a>
    <a href="javascript:;" id="publish-task" class="weui_btn weui_btn_warn">发布任务</a>
</div>
<footer style="border: #ee7800 1px dashed; overflow: hidden;width: 90%;margin: 15px auto;">
    <a href="/Bang/Index/huochepiao"
       id="huochepiao" class="weui_btn" had-use="{{$huochepiao_had_use}}"
       style="color: #04BE02;border: 1.5px dashed #04BE02;">帮取火车票</a>
    <a href="/Bang/Index/jiaoche"
       class="weui_btn" style="color: #5A5A5A;border: 1.5px dashed #5A5A5A;">预约叫车</a>
</footer>
<script src="/public/common/plugin/owl-carousel/owl.carousel.js"></script>
<script>
    $("#carousel-example-generic").owlCarousel({
        // Most important owl features
        items: 5,
        itemsCustom: false,
        itemsDesktop: [1199, 4],
        itemsDesktopSmall: [980, 3],
        itemsTablet: [768, 2],
        itemsTabletSmall: false,
        itemsMobile: [479, 1],
        singleItem: true,
        itemsScaleUp: false,
        //Basic Speeds
        slideSpeed: 200,
        paginationSpeed: 800,
        rewindSpeed: 1000,
        //Autoplay
        autoPlay: true,
        stopOnHover: true,
        // Navigation
        navigation: false,
        navigationText: ["prev", "next"],
        rewindNav: true,
        scrollPerPage: false,
        //Pagination
        pagination: false,
        paginationNumbers: false,
        // Responsive
        responsive: true,
        responsiveRefreshRate: 200,
        responsiveBaseWidth: window,
        // CSS Styles
        baseClass: "owl-carousel",
        theme: "owl-theme",
        //Lazy load
        lazyLoad: false,
        lazyFollow: true,
        lazyEffect: "fade",
        //Auto height
        autoHeight: false,
        //JSON
        jsonPath: false,
        jsonSuccess: false,
        //Mouse Events
        dragBeforeAnimFinish: true,
        mouseDrag: true,
        touchDrag: true,
        //Transitions
        transitionStyle: false,
        // Other
        addClassActive: false,
        //Callbacks
        beforeUpdate: false,
        afterUpdate: false,
        beforeInit: false,
        afterInit: false,
        beforeMove: false,
        afterMove: false,
        afterAction: false,
        startDragging: false,
        afterLazyLoad: false
    });
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
    pathName = window.location.pathname;
    pathIndex=pathName.indexOf('index.php/');
    scriptPath = 'http://' + window.location.host +((pathIndex!=-1)?'/index.php':'') ;
     document.getElementById('get-task').onclick = function () {
        /*if (!document.getElementById('had-reg')) {
         MDialog.open({
         showTitle: false,
         content: "<div style='text-align: center;'>为了会更好地为您服务，</div><div style='text-align: center;'>您需要补全信息后才可以领取任务！</div>",
         contentType: "html",
         onPositive: function () {
         window.location.href = scriptPath + '/Bang/Index/register';
         }
         });
         return false;
         }*/
        window.location.href = scriptPath + '/Bang/Index/get';
    };
    document.getElementById('publish-task').onclick = function () {
        //alert(document.body.innerHTML);
        window.location.href = scriptPath + '/Bang/Index/publish';
    };
    document.getElementById('huochepiao').onclick = function () {
        if (this.getAttribute("had-use") == 'yes') {
            window.location.href = scriptPath + '/Bang/Index/msg';
            return false;
        }
        if (document.getElementById('had-reg')) {
            sendData(null, scriptPath + '/Bang/Index/huochepiaogetstraight', function (errCode) {
                switch (errCode) {
                    case 0:
                        window.location.href = 'http://' + window.location.host +
                                pathName.substr(0, pathName.indexOf('.php/') + 4) + '/Bang/Index/msg';
                        break;
                    case 2:
                        window.location.href = scriptPath + '/Bang/Index/msg';
                        break;
                    case -3:
                        window.location.reload();
                        break;
                    default:
                        console.log(errCode);
                }
            });
            return false;
        }
    };
</script>
</body>
</html>