<?php define('__DIRECT_REQUEST__', ''); require('init.php');

if(!$userId || !($userType === 'admin' || $userType === 'system')) {
	header('Location: login.php');
	exit(0);
}
$manage = TRUE;

if(isset($_GET['id'])) {
	$items = $db->filteredQuery('SELECT * FROM `item` WHERE `item_id`=%d', array($_GET['id']), FALSE, TRUE);
	if(!count($items)) exit(0);
	$item = $items[0];
} else {
	header('Location: index.php');
	exit(0);
}

$is = explode("\n", $item['form']);
$cols = array();
$colNames = array();
foreach($is as $i) {
	if(!preg_match('/^([0-9a-z_]+):([^\|]+)\|([^\|]*)\|([^\r\n]+)/i', $i, $a))
		continue;
	if(preg_match('/^hint[ \t$]/', trim($a[4])))
		continue;
	$cols[] = $a[1];
	$colNames[] = trim($a[2]);
}

$formData = $db->filteredQuery('SELECT * FROM `user` NATURAL JOIN `item` NATURAL LEFT OUTER JOIN `item_user` WHERE `item_id`=%d ORDER BY `user_id`', array($_GET['id']), FALSE, TRUE);

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>结果统计 | <?php echo htmlspecialchars($item['title']); ?> | 班务管理系统</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="wrapper">
		<div class="header">
			<h1><a href="index.php">班务管理系统</a></h1>
			<p>设计UI很花时间的，各位放过我吧 &gt;.&lt;</p>
			<hr>
		</div>
		<div class="content content_wide">
			<h1><a href="item.php?id=<?php echo $item['item_id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a></h1>
			<table border="1" cellspacing="0" cellpadding="3">
				<thead><tr>
					<th>用户</th>
					<th>显示名</th>
					<?php foreach($colNames as $v) echo '<th>'.htmlspecialchars($v).'</th>'; ?>
					<th>修改时间</th>
				</tr></thead>
				<tbody>
					<?php foreach($formData as $v) {
						echo '<tr><td>'.$v['user_id'].'</td>';
						echo '<td>'.htmlspecialchars($v['display_name']).'</td>';
						$t = unserialize($v['user_form']);
						foreach($cols as $col)
							echo '<td>'.$t[$col].'</td>';
						if($v['user_time'])
							echo '<td>'.date('Y-m-d H:i', $v['user_time']).'</td></tr>';
						else
							echo '<td></td></tr>';
					} ?>
				</tbody>
				<tfoot><tr>
					<th>用户</th>
					<th>显示名</th>
					<?php foreach($colNames as $v) echo '<th>'.htmlspecialchars($v).'</th>'; ?>
					<th>修改时间</th>
				</tr></tfoot>
			</table>
		</div>
		<div class="footer">
			<hr>
			<p>用户：<?php echo $userId; ?> <a href="password.php">更改密码</a> <a href="login.php?logout=<?php echo $userId; ?>">登出</a> <?php if($manage) { ?>| <a href="user_management.php">管理用户</a> <?php } ?>| 班务管理系统 | Copyright 2013 LastLeaf</p>
		</div>
	</div>
</body>
</html>