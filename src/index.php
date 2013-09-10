<?php define('__DIRECT_REQUEST__', ''); require('init.php');

if(!$userId) {
	header('Location: login.php');
	exit(0);
}
$manage = FALSE;
if($userType === 'admin' || $userType === 'system')
	$manage = TRUE;

$items = $db->filteredQuery('SELECT `item_id`,`title`,`time`,`user_time` FROM `item` NATURAL LEFT OUTER JOIN (SELECT `item_id`,`user_time` FROM `item_user` WHERE `user_id`=%s) AS `t` ORDER BY `time` DESC', array($userId), TRUE,TRUE);

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>首页 | 班务管理系统</title>
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
			<ul class="main_list">
				<?php foreach($items as $v) { ?>
					<li <?php if(!$v['user_time']) echo 'style="font-weight:bold;"' ?>>
						<a href="item.php?id=<?php echo $v['item_id']; ?>">
							<?php echo '<span class="main_list_time">'.date('Y-m-d H:i', $v['time']).' - </span>'.htmlspecialchars($v['title']); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
			<?php if($manage) { ?><p><a href="edit_item.php">添加内容项目</a></p><?php } ?>
		</div>
		<div class="footer">
			<hr>
			<p>用户：<?php echo $userId; ?> <a href="password.php">更改密码</a> <a href="login.php?logout=<?php echo $userId; ?>">登出</a> <?php if($manage) { ?>| <a href="user_management.php">管理用户</a> <?php } ?>| 班务管理系统 | Copyright 2013 LastLeaf</p>
		</div>
	</div>
</body>
</html>