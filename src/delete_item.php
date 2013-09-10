<?php define('__DIRECT_REQUEST__', ''); require('init.php');

if(!$userId || !($userType === 'admin' || $userType === 'system')) {
	header('Location: login.php');
	exit(0);
}
$manage = TRUE;

if(isset($_POST['continue']) && isset($_GET['id'])) {
	$db->del('item', '`item_id`=%d', array($_GET['id']), FALSE, TRUE);
	header('Location: index.php');
	exit(0);
}

if(isset($_GET['id'])) {
	$items = $db->filteredQuery('SELECT * FROM `item` WHERE `item_id`=%d', array($_GET['id']), FALSE, TRUE);
	if(!count($items)) exit(0);
	$item = $items[0];
} else {
	header('Location: index.php');
	exit(0);
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>删除确认 | <?php echo htmlspecialchars($item['title']); ?> | 班务管理系统</title>
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
			<form method="POST" action="">
				<input type="hidden" name="continue" value="continue">
				<p>即将删除：<a href="item.php?id=<?php echo $item['item_id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a></p>
				<p><input type="submit" value="继续"></p>
			</form>
		</div>
		<div class="footer">
			<hr>
			<p>用户：<?php echo $userId; ?> <a href="password.php">更改密码</a> <a href="login.php?logout=<?php echo $userId; ?>">登出</a> <?php if($manage) { ?>| <a href="user_management.php">管理用户</a> <?php } ?>| 班务管理系统 | Copyright 2013 LastLeaf</p>
		</div>
	</div>
</body>
</html>