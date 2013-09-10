<?php define('__DIRECT_REQUEST__', ''); require('init.php');

if(!$userId) {
	header('Location: login.php');
	exit(0);
}
$manage = FALSE;
if($userType === 'admin' || $userType === 'system')
	$manage = TRUE;

$hint = '';
if(isset($_POST['password']) && isset($_POST['password_retype'])) {
	if($_POST['password'] !== $_POST['password_retype'])
		$hint = '两次输入的密码不一致';
	else {
		$db->update('user', array('password' => $crypt->hash($_POST['password'])), '`user_id`=%s', array($userId), TRUE, TRUE);
		header('Location: index.php');
		exit(0);
	}
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>修改密码 | 班务管理系统</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="wrapper">
		<div class="header">
			<h1><a href="index.php">班务管理系统</a></h1>
			<p>设计UI很花时间的，各位放过我吧 &gt;.&lt;</p>
			<hr>
		</div>
		<div class="content">
			<p class="hint"><?php echo $hint; ?></p>
			<form method="POST" action="">
				<p>输入两次新密码</p>
				<p><input type="password" name="password"></p>
				<p><input type="password" name="password_retype"></p>
				<p><input type="submit" value="确认"></p>
			</form>
		</div>
		<div class="footer">
			<hr>
			<p>用户：<?php echo $userId; ?> <a href="login.php?logout=<?php echo $userId; ?>">登出</a> <?php if($manage) { ?>| <a href="user_management.php">管理用户</a> <?php } ?>| 班务管理系统 | Copyright 2013 LastLeaf</p>
		</div>
	</div>
</body>
</html>