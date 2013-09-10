<?php define('__DIRECT_REQUEST__', ''); require('init.php');

$hint = '';
if(isset($_POST['userId']) && isset($_POST['password'])) {
	$_POST['userId'] = strtolower($_POST['userId']);
	$r = $db->filteredQuery('SELECT `password` FROM `user` WHERE `user_id`=%s', array($_POST['userId']), TRUE, TRUE);
	if(!count($r)) {
		$hint = '用户不存在！';
	} else {
		$authStr = $r[0]['password'];
		if(!$authStr || $crypt->check($_POST['password'], $authStr)) {
			$_SESSION['userId'] = $_POST['userId'];
			header('Location: index.php');
			exit(0);
		} else {
			$hint = '密码错误！';
		}
	}
} else if(isset($_GET['logout']) && $_GET['logout'] === $userId) {
	$_SESSION['userId'] = '';
	$userId = '';
	$hint = '已登出';
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>登录 | 班务管理系统</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="wrapper">
		<div class="header">
			<h1>班务管理系统</h1>
			<p>设计UI很花时间的，各位放过我吧 &gt;.&lt;</p>
			<hr>
		</div>
		<div class="content">
			<p class="hint"><?php echo $hint; ?></p>
			<form method="POST" action="login.php">
				<p><label for="userId">用户</label> <input type="text" id="userId" name="userId"></p>
				<p><label for="password">密码</label> <input type="password" id="password" name="password"></p>
				<p><input type="submit" value="登录"></p>
			</form>
		</div>
		<div class="footer">
			<hr>
			<p>班务管理系统 | Copyright 2013 LastLeaf</p>
		</div>
	</div>
</body>
</html>