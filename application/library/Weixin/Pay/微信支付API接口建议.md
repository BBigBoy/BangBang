#2015/11/30第二次修改：
##1、在接口参数设置及校验中，现有实现比较繁琐，需要一种只需简单配置，统一的校验方式。
    初步考虑 使用 方法func_get_args()，配合一定的规则实现； 
    func_get_args()：返回包含所有参数的数组 。

#2015/11/30第一次修改：
##1、微信支付API部分代码可从SVN仓库中获取：
    svn://182.92.68.92/pro/Application/Platform/Common/WXPay
    后期关于此部分代码的拉取及修改提交请直接在这里面操作。
    svn账户密码都是：fuyang。
    公司RDS数据库信息：
    外网连接：rds10526056301a36q90.mysql.rds.aliyuncs.com
    账号：fuyang
    密码：fuyang1314
    端口：3306
    数据库：fuyang_database
##2、类似于“类的成员属性”、“构造函数”的注释可以省略，修改为每个对象所能实现的功能较好；
##3、类似于“$createparam”的命名方式请尽量细化，
    从后期使用，可判断这个对象是用于设置接口方法的传参的，可重新进行命名；
##4、在类Platform\Common\WXPay\WXPayParamValidate第153行及后面多行中
    存在$errorMsg与$this->errorMsg 指向不明的问题；
##5、类Platform\Common\WXPay\WXPayParamValidate中，
    类似于isStringParam方法存在命名与其功能存在一定差距的问题，
    可考虑修改方法名及其注释；
##6、类Platform\Common\WXPay\WXPayAPI中，
    存在许多header('Content-Type:text/html;charset=UTF-8')方法调用，此句应该可省略，
    或者如果确实需要，可以在构造函数中直接使用，望查询之后对此做一下处理；
##7、类Platform\Common\WXPay\WXPayCommon中，
    所有方法几乎都可以成为静态类方法，另：postxmlSSLCurl及postXmlCurl可做一下合并；
    另，考虑到此类中的方法是公共常用方法，可考虑将其加入Platform模块下Common文件夹中的function.php文件中
    （仅仅考虑，如果是基于模块独立性，可保持现状）；
##8、类Platform\Common\WXPay\WXPayManage中，
    针对每种支付方式都设计了对应记录日志的方法，非常好，但是方法中针对每种支付方式，
    都设计了专门的表存储，这样很难进行统一日志管理，建议归纳分析下现有表中的可抽象的部分，
    将所有支付的日志归纳到一个表中；同时也可以抽象出一个专用的记录支付日志的方法，
    每种支付方式的方法只需按照要求组装需要保存的数据，再调用这个方法即可；
##9、类Platform\Common\WXPay\WXPay中，
    类似于JSAPI的方法命名也请再思考一下更加准确的命名；
##10、后续修改意见希望咱们把前面的建议处理后继续修改。
    总的来说，支付这一部分代码是经过认真分析，有思想的代码，在很多标准的处理方式上都非常好，
    感谢你的认真对待。支付这部分代码涉及资金安全，需要多加思虑，烦劳费心。


