/**
 * Created by BigBigBoy on 2015/12/23.
 */

function validate_mobile(field) {
    /*
     表达式分析：
     “/”代表一个正则表达式。
     “^”代表字符串的开始位置，“$”代表字符串的结束位置。
     “?”代表匹配前面的字符一个或零个，所以这里0?的意思是手机号码可以以0开头或不以0开头。
     接下的部分验证11位的手机号码，先从13开始，因为从130-139都有所以可选区间是[0-9]，15开头的号码没有154所以[]里面没有4这个数字，
     当然也可以写成[0-35-9]，下面18和14开的号码同上。
     小括号括起来的代表一个子表达式，里面是4个可选分支分别用“|”来区分开来，在正则中“|”的优先级是最低的，
     这里每个分支匹配的都是3个字符（一个[]只能匹配一个字符，里面是可选的意思），
     也就是手机号码的前3位数字，那么后面还有8位数字需要匹配，可以是0-9的任意字符，所以是“[0-9]{8}”，{}中的数字代表匹配前面字符的个数。分析完毕。*/
    with (field) {
        var regMobile = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
        return regMobile.test(value);
    }
}
function validate_required(field) {
    with (field) {
        return !(value == null || value.trim() == "");
    }
}
function validate_email(field) {
    with (field) {
        apos = value.indexOf("@");
        dotpos = value.lastIndexOf(".");
        return !(apos < 1 || dotpos - apos < 2);
    }
}