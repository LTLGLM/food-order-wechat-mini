<?php if (!defined('IN_INSTALL')) exit('Request Error!'); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>餐饮点单系统 安装向导 - 安装说明</title>
    <link href="/install/templates/style/install.css" type="text/css" rel="stylesheet"/>
    <link rel="icon" href="/favicon.png" type="image/x-icon">
    <script type="text/javascript" src="../assets/plugins/jquery/jquery.min.js"></script>
</head>
<body>
<div class="header"></div>
<div class="mainBody">
    <div class="text">
        <h3>安装说明</h3>
        <p>欢迎使用当前项目的安装向导。继续安装前，请确认你已经准备好数据库账号信息，并了解本向导会写入项目根目录下的 <code>.env</code> 文件。</p>
        <div class="hr_8"></div>
        <p>本系统适用于本地调试和自部署场景。你可以基于自己的业务需要进行二次开发、替换品牌信息和调整功能配置。</p>
        <div class="hr_8"></div>
        <p>安装过程中将会创建数据库结构、导入基础数据，并生成初始化后台管理员账号。除非你主动勾选，否则不会导入测试数据。</p>
        <div class="hr_8"></div>
        <p>请妥善保管数据库配置与后台管理员凭据。安装完成后，建议立即检查站点基础设置、支付配置、短信配置和小程序端域名配置。</p>
        <div class="hr_8"></div>
        <p>继续安装即表示你理解并接受本地自部署带来的配置、运维和数据安全责任。</p>
    </div>
</div>
<div class="footer">
    <span class="step"></span>
    <span class="copyright">© 2017-<?php echo date('Y'); ?> 餐饮点单系统</span>
    <span class="formSubBtn">
        <a href="javascript:void(0);" onclick="window.close();return false;" class="back">取消</a>
        <a href="/install/index.php?s=1" class="submit">继续</a>
    </span>
</div>
</body>
</html>
