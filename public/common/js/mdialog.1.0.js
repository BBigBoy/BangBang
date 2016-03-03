/**
 * Created by 14261 on 2015/11/19.
 */
(function () {
    //默认属性
    var config = {
        title: "消息",
        content: "",
        width: window.innerWidth / 1.3,
        showTitle: true,
        type: "alert",
        contentType:"text",
        topicColor: "#0088ff",
        canDismiss: false,
        onOpened: function () {

        },
        onPositive: function () {

        },
        onNegative: function () {

        },
        onClose: function () {

        }
    };
    var btnDefaultColor;
    var btnClicking = function (e) {
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (e.type == "touchstart") {
            btnDefaultColor = e.target.style.color;
            e.target.style.color = "#999999";
        } else if (e.type == "touchend") {
            e.target.style.color = btnDefaultColor;
        }
    };
    window.MDialog = {
        /**
         * @param args
         * config.title String 标题 默认“消息”
         * config.content String 对话框显示文字内容
         * config.showTitle Boolean;是否显示标题栏 默认true
         * config.width 格式：Number 或者 99px 99em 对话框宽度   默认是 window宽度除以 1.3
         * config.type Enum{"alert","confirm"} 对话框是否有“取消”按钮默认 alert
         * config.contentType Enum{"text","html"} 显示内容为text还是html 默认 text
         * config.topicColor 格式：#000000 ,rgb(255,255,255) rgba(255,255,255,0.2) 标题栏及颜色描述   默认是 #0088ff
         * config.canDismiss Boolean 是否允点击对话框以外区域关闭对话框 默认为 false
         * config.onOpened  Function 当对话框显示后执行
         * config.onPositive Function 当点击对话框确认按钮执行
         * config.onNegative Function 当点击对话框取消按钮执行
         * config.onClose Function 对话框关闭前执行
         *
         * @return dialog
         * dialog.close();
         */
        open: function (args) {
            //复制config属性
            if (args) {
                for (var pro in config) {
                    if (args[pro] != 'undefined' && args[pro] != null) {
                        if (pro == "onOpened" || pro == "onNegative" || pro == "onPositive" || pro == "onClose") {
                            if (!(args[pro] instanceof Function)) {
                                throw new TypeError(pro + " 不是一个函数");
                            }
                        }
                    } else {
                        args[pro] = config[pro];
                    }
                }
            } else {
                args = {};
                for (var pro in config) {
                    args[pro] = config[pro];
                }
            }
            //创建背景
            var alertBg = document.createElement("div");
            alertBg.style.position = "fixed";
            alertBg.style.margin = 0;
            alertBg.style.padding = 0;
            alertBg.style.backgroundColor = "rgba(0,0,0,0.2)";
            alertBg.style.top = 0;
            alertBg.style.bottom = 0;
            alertBg.style.left = 0;
            alertBg.style.right = 0;
            alertBg.style.zIndex = "9999";
            alertBg.onclick = function (e) {
                e.stopPropagation();
                e.stopImmediatePropagation();
            };
            //创建对话框主体
            var alert = document.createElement("div");
            alert.style.width = args.width instanceof Number ? args.width + "px" : (/^\d+(\.\d+)?$/gi.test(args.width) ? args.width + "px" : args.width);
            alert.style.position = "absolute";
            alert.style.backgroundColor = "white";
            alert.style.borderRadius = "10px";
            alert.style.overflow = "hidden";
            alert.style.paddingBottom = "1.4em";
            alertBg.style.visibility = "hidden";
            alert.addEventListener("touchstart", function (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            });
            alert.addEventListener("touchend", function (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            });


            //创建标题栏
            if (args.showTitle) {
                var title = document.createElement("h3");
                title.innerText = args.title;
                title.style.marginTop = 0;
                title.style.marginBottom = 0;
                title.style.paddingTop = "0.4em";
                title.style.paddingBottom = "0.4em";
                title.style.backgroundColor = args.topicColor;
                title.style.color = "white";
                title.style.paddingLeft = "1em";
                title.style.paddingRight = "1em";
                title.style.webkitUserSelect = "none";
                title.style.userSelect = "none";
                alert.appendChild(title);
            }
            //创建内容体
            var contentBox = document.createElement("div");
            contentBox.style.paddingTop = "1.5em";
            contentBox.style.paddingBottom = "1.5em";
            contentBox.style.paddingLeft = "2em";
            contentBox.style.paddingRight = "2em";
            contentBox.style.minHeight = "5em";
            if(args.contentType=="text"){
                contentBox.innerText = args.content;
            }else if(args.contentType=="html"){
                contentBox.innerHTML=args.content;
            }else{
                throw new TypeError("contentType只能是text/html");
            }

            contentBox.style.fontFamily = "\"微软雅黑\",\"黑体\",serif;";

            //创建footer
            var footBox = document.createElement("div");
            footBox.style.height = "1.9em";
            footBox.style.width = "100%";
            footBox.style.position = "absolute";
            footBox.style.bottom = "0";
            footBox.style.zoom = "1";
            footBox.style.overflow = "hidden";
            footBox.style.borderTop = "1px solid " + args.topicColor;
            footBox.style.padding = "0";
            footBox.style.margin = "0";
            footBox.style.display = "box";
            footBox.style.display = "-webkit-box";
            footBox.style.webkitBoxOrient = "horizontal";
            footBox.style.boxOrient = "horizontal";


            if (args.type == "confirm") {
                var cancelBtn = document.createElement("div");
                cancelBtn.style.backgroundColor = "white";
                cancelBtn.style.textAlign = "center";
                cancelBtn.style.lineHeight = "1.9em";
                cancelBtn.style.fontSize = "1.1em";
                cancelBtn.style.webkitUserSelect = "none";
                cancelBtn.style.userSelect = "none";
                cancelBtn.style.color = args.topicColor;
                cancelBtn.style.webkitBoxFlex = "1";
                cancelBtn.style.boxFlex = "1";
                cancelBtn.style.boxShadow = "1px 1px 1px 2px transparent inset";
                cancelBtn.style.outline = "none";
                cancelBtn.style.textDecoration = "none";
                cancelBtn.innerText = "取消";
                cancelBtn.addEventListener("touchstart", btnClicking);
                cancelBtn.addEventListener("touchend", btnClicking);
                cancelBtn.onclick = function (e) {
                    e.preventDefault();
                    if (args.onNegative() !== false) {
                        args.onClose();
                        alertBg.parentNode.removeChild(alertBg);
                    }
                };
                footBox.appendChild(cancelBtn);
                //增加中线
                var btnMiddleLine = document.createElement("div");
                btnMiddleLine.style.background = args.topicColor;
                btnMiddleLine.style.width = "1px";
                footBox.appendChild(btnMiddleLine);
            }

            //footer中填充按钮
            var alertBtn = document.createElement("div");
            alertBtn.style.backgroundColor = "white";
            alertBtn.style.textAlign = "center";
            alertBtn.style.lineHeight = "1.9em";
            alertBtn.style.fontSize = "1.1em";
            alertBtn.style.webkitUserSelect = "none";
            alertBtn.style.mozUserSelect = "none";
            alertBtn.style.msUserSelect = "none";
            alertBtn.style.oUserSelect = "none";
            alertBtn.style.userSelect = "none";
            alertBtn.style.color = args.topicColor;
            alertBtn.style.webkitBoxFlex = "1";
            alertBtn.style.boxFlex = "1";
            alertBtn.style.boxShadow = "1px 1px 1px 2px transparent inset";
            alertBtn.style.outline = "none";
            alertBtn.style.textDecoration = "none";
            alertBtn.innerText = "确认";
            alertBtn.addEventListener("touchstart", btnClicking);
            alertBtn.addEventListener("touchend", btnClicking);
            alertBtn.onclick = function (e) {
                e.preventDefault();
                if (args.onPositive() !== false) {
                    args.onClose();
                    alertBg.parentNode.removeChild(alertBg);
                }
            };
            footBox.appendChild(alertBtn);


            if (args.canDismiss) {
                alertBg.onclick = function (e) {
                    if (e.target == this) {
                        document.body.removeChild(this);
                    }
                }
            }
            alert.onclick = function (e) {
                e.stopPropagation();
                e.stopImmediatePropagation();
            };

            //显示
            alert.appendChild(contentBox);
            alert.appendChild(footBox);
            alertBg.appendChild(alert);
            document.body.appendChild(alertBg);
            //修正位置
            alert.style.left = (alertBg.clientWidth - alert.clientWidth) / 2 + "px";
            alert.style.top = (alertBg.clientHeight - alert.clientHeight) / 2 - alert.clientHeight / 3 + "px";
            alertBg.style.visibility = "visible";
            args.onOpened();

            return {
                close: function () {
                    alertBg.parentNode.removeChild(alertBg);
                }
            };
        }
    }
})();