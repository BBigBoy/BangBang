<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=yes">
    <title>宿舍叫车-爱帮忙</title>
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
        userId = userId ? userId : '{{$loginUserInfo.id}}';
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
<img src="/public/bang/image/top-tuanwei.jpg" style="width: 100%;border-bottom: #ee7800 1px dashed;">
<div style="margin: 3% 6px;border: #ee7800 1px dashed;padding: 5px;">

    <p>在宿舍与校门道路间拖着归家时大件的行李，寒风刺骨，“路途漫漫”，是否也会需要温暖的小车接送？</p>

    <p>“爱帮忙”帮您联系了很多提供接送服务的司机 ，您只需要拨打他们的电话即可享受宿舍到火车站的接送服务！（请自行与司机商量价格）</p>

    <p>小伙伴们还等待什么，赶快转发朋友圈让大家看到这个福利吧！</p>
</div>
<table class="ui-table ui-border-tb" style="margin-top: 5%;margin-bottom: 8%;">
    <thead>
    <tr>
        <th>编号</th>
        <th>车牌号</th>
        <th>司机</th>
        <th>联系方式</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>01</td>
        <td>陕AW206G</td>
        <td>姜亚斌</td>
        <td>15229391867</td>
    </tr>

    <tr>
        <td>02</td>
        <td>陕VUS123</td>
        <td>石凯峰</td>
        <td>13279396994</td>
    </tr>

    <tr>
        <td>03</td>
        <td>陕VJL616</td>
        <td>刘迎昌</td>
        <td>18291425357</td>
    </tr>

    <tr>
        <td>04</td>
        <td>陕VJD618</td>
        <td>金浩东</td>
        <td>18192903211</td>
    </tr>

    <tr>
        <td>05</td>
        <td>陕VMG456</td>
        <td>闫青国</td>
        <td>13891858834</td>
    </tr>

    <tr>
        <td>06</td>
        <td>陕VJ8687</td>
        <td>郭宏波</td>
        <td>15529607017</td>
    </tr>

    <tr>
        <td>07</td>
        <td>陕VCO127</td>
        <td>郭满印</td>
        <td>13572405008</td>
    </tr>

    <tr>
        <td>08</td>
        <td>陕VAK128</td>
        <td>孙光军</td>
        <td>15091766879</td>
    </tr>

    <tr>
        <td>09</td>
        <td>陕VLY089</td>
        <td>杨光浩</td>
        <td>15829793948</td>
    </tr>

    <tr>
        <td>10</td>
        <td>陕KTW688</td>
        <td>张力</td>
        <td>13892738493</td>
    </tr>

    <tr>
        <td>11</td>
        <td>陕VJF123</td>
        <td>董银波</td>
        <td>13072914835</td>
    </tr>

    <tr>
        <td>12</td>
        <td>陕VGH689</td>
        <td>候兵利</td>
        <td>13572157234</td>
    </tr>

    <tr>
        <td>13</td>
        <td>陕VCX619</td>
        <td>徐新国</td>
        <td>13709124886</td>
    </tr>

    <tr>
        <td>14</td>
        <td>陕VAV680</td>
        <td>徐建国</td>
        <td>13109560278</td>
    </tr>

    <tr>
        <td>15</td>
        <td>陕VTQ889</td>
        <td>王红军</td>
        <td>15929721623</td>
    </tr>

    <tr>
        <td>16</td>
        <td>陕VCC639</td>
        <td>马有文</td>
        <td>15109273722</td>
    </tr>

    <tr>
        <td>17</td>
        <td>陕VHX788</td>
        <td>吴涛</td>
        <td>18829351059</td>
    </tr>

    <tr>
        <td>18</td>
        <td>陕V NG889</td>
        <td>丁瑞龙</td>
        <td>15002930407</td>
    </tr>

    <tr>
        <td>19</td>
        <td>陕VKK030</td>
        <td>高伟明</td>
        <td>13468950712</td>
    </tr>

    <tr>
        <td>20</td>
        <td>陕VMY118</td>
        <td>吴朋</td>
        <td>18066587034</td>
    </tr>
    </tbody>
</table>

</body>
</html>