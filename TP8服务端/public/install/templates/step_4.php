<?php if(!defined('IN_INSTALL')) exit('Request Error!'); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>餐饮点单系统 安装向导 - 安装完成</title>
<link href="/install/templates/style/install.css" type="text/css" rel="stylesheet" />
<link rel="icon" href="/favicon.png" type="image/x-icon">
<script type="text/javascript" src="../assets/plugins/jquery/jquery.min.js"></script>
</head>
<body>
<div class="header"></div>
<div class="mainBody">
	<div class="note">
		<div class="complete">
		    <strong>安装已完成，现在你可以：</strong><br />
			<a href="../">访问首页</a>
			<span>或</span>
			<a href="../store/">登录后台</a><br /><br />
			<span>如需重新安装，请先手动删除 /public/install/install.lock 文件。</span>
			<br /><br />
			<span>建议安装完成后立即备份数据库，并检查站点基础设置、支付配置和小程序端域名。</span>
		</div>
	</div>
</div>
<div class="footer">
    <span class="step4"></span>
    <span class="copyright">© 2017-<?php echo date('Y'); ?> 餐饮点单系统</span>
</div>
</body>
</html>
