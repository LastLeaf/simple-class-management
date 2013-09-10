<?php define('__DIRECT_REQUEST__', ''); require('init.php');

if(!$userId || ($userType !== 'admin' && $userType !== 'system')) {
	header('Location: login.php');
	exit(0);
}

if(isset($_POST['update'])) {
	$us = explode("\n", $_POST['update']);
	foreach($us as $u) {
		if(!preg_match('/^[ \t]*([0-9a-z_]+)[ \t]+([^ \t\r\n]+)[ \t]+([^\t\r\n]+)/i', $u, $a))
			continue;
		$db->insert('user', array(
			'user_id' => $a[1],
			'display_name' => $a[3],
			'password' => $crypt->hash($a[2]),
			'type' => 'common'
		), TRUE, TRUE);
	}
} else if(isset($_POST['delete'])) {
	$us = explode("\n", $_POST['delete']);
	foreach($us as $u) {
		if(!preg_match('/^[ \t]*([0-9a-z_]+)/i', $u, $a))
			continue;
		$db->del('user', '`user_id`=%s', array($a[1]), TRUE, TRUE);
	}
} else if(isset($_POST['admin'])) {
	$us = explode("\n", $_POST['admin']);
	foreach($us as $u) {
		if(!preg_match('/^[ \t]*([0-9a-z_]+)/i', $u, $a))
			continue;
		if($a[1] === $userId) continue;
		$db->update('user', array('type' => 'admin'), '`user_id`=%s', array($a[1]), TRUE, TRUE);
	}
} else if(isset($_POST['common'])) {
	$us = explode("\n", $_POST['common']);
	foreach($us as $u) {
		if(!preg_match('/^[ \t]*([0-9a-z_]+)/i', $u, $a))
			continue;
		if($a[1] === $userId) continue;
		$db->update('user', array('type' => 'common'), '`user_id`=%s', array($a[1]), TRUE, TRUE);
	}
}

$users = $db->query('SELECT `user_id`,`display_name`,`type` FROM `user` ORDER BY `user_id`', TRUE);

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>用户管理 | 班务管理系统</title>
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
			<ul class="user_list">
				<?php foreach($users as $v) { ?>
					<li <?php if($v['type'] !== 'common') echo 'style="font-weight:bold;"' ?>>
						<?php echo htmlspecialchars($v['display_name']) . ' (' . $v['user_id'] . ')'; ?>
					</li>
				<?php } ?>
			</ul>
			<hr>
			<form method="POST" action="">
				<p>添加/更新普通用户 （每行：用户 密码 显示名）</p>
				<textarea name="update"></textarea>
				<input type="submit" value="确认">
			</form>
			<hr>
			<form method="POST" action="">
				<p>删除用户 (每行：用户)</p>
				<textarea name="delete"></textarea>
				<input type="submit" value="确认">
			</form>
			<hr>
			<form method="POST" action="">
				<p>提升权限 (每行：用户)</p>
				<textarea name="admin"></textarea>
				<input type="submit" value="确认">
			</form>
			<hr>
			<form method="POST" action="">
				<p>降低权限 (每行：用户)</p>
				<textarea name="common"></textarea>
				<input type="submit" value="确认">
			</form>
		</div>
		<div class="footer">
			<hr>
			<p>用户：<?php echo $userId; ?> <a href="login.php?logout=<?php echo $userId; ?>">登出</a> | 班务管理系统 | Copyright 2013 LastLeaf</p>
		</div>
	</div>
</body>
</html>