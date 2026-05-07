<?php if (!defined('IN_INSTALL')) exit('Request Error!'); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>餐饮点单系统 安装向导 - 配置数据文件</title>
    <link href="/install/templates/style/install.css" type="text/css" rel="stylesheet"/>
    <link rel="icon" href="/favicon.png" type="image/x-icon">
    <script type="text/javascript" src="../assets/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/install/templates/js/forms.js"></script>
</head>
<body>
<form name="form" id="form" method="post" action="/install/index.php">
    <div class="header"></div>
    <div class="mainBody">
        <div class="table">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td height="40" colspan="2" align="left"><span class="title">填写数据库信息</span></td>
                </tr>
                <tr>
                    <td width="30%" height="40" align="right">数据库地址：</td>
                    <td><input type="text" name="dbhost" id="dbhost" class="input" value="localhost"/>
                        <span class="cnote">本地数据库服务地址通常为 localhost 或 127.0.0.1</span></td>
                </tr>
                <tr>
                    <td height="40" align="right">数据库名称：</td>
                    <td><input type="text" name="dbname" id="dbname" class="input" value="plat"/></td>
                </tr>
                <tr>
                    <td width="30%" height="40" align="right">数据库端口：</td>
                    <td><input type="text" name="dbport" id="dbport" class="input" value="3306"/>
                        <span class="cnote">MySQL 默认端口为 3306</span></td>
                </tr>
                <tr>
                    <td height="40" align="right">数据库用户：</td>
                    <td><input type="text" name="dbuser" id="dbuser" class="input" value="root"/></td>
                </tr>
                <tr>
                    <td height="40" align="right">数据库密码：</td>
                    <td><input type="password" name="dbpwd" id="dbpwd" class="input" onblur="CheckPwd()"/>
                        <span class="cnote"><span id="cpwdTxt"></span></span>
                        <input type="hidden" name="cpwd" id="cpwd" value="false"></td>
                </tr>

                <tr>
                    <td height="40" colspan="2" align="left"><span class="title">初始化后台账号</span></td>
                </tr>
                <tr>
                    <td height="40" align="right">管理员账号：</td>
                    <td><input type="text" name="admin_account" id="admin_account" class="input" value=""/>
                        <span class="cnote">用于登录后台，建议填写你自己的管理员账号</span></td>
                </tr>
                <tr>
                    <td height="40" align="right">管理员密码：</td>
                    <td><input type="password" name="admin_password" id="admin_password" class="input" value=""/>
                        <span class="cnote">安装完成后可在后台继续修改</span></td>
                </tr>
                <tr>
                    <td height="40" align="right">安装测试数据：</td>
                    <td><input type="checkbox" name="testdata" value="true"/>是</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="footer">
        <span class="step3"></span>
        <span class="copyright">© 2017-<?php echo date('Y'); ?> 餐饮点单系统</span>
        <span class="formSubBtn">
            <a href="javascript:void(0);" onclick="history.go(-1);return false;" class="back">返回</a>
            <a href="javascript:void(0);" onclick="CheckForm();return false;" class="submit">开始安装</a>
            <input type="hidden" name="s" id="s" value="3">
        </span>
    </div>
</form>
</body>
</html>
