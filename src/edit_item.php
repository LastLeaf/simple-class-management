<?php define('__DIRECT_REQUEST__', ''); require('init.php');

if(!$userId || !($userType === 'admin' || $userType === 'system')) {
	header('Location: login.php');
	exit(0);
}
$manage = TRUE;

if(isset($_POST['title']) && isset($_POST['content']) && isset($_POST['form'])) {
	$a = array(
		'title' => $_POST['title'],
		'content' => $_POST['content'],
		'form' => $_POST['form'],
		'time' => time()
	);
	if(isset($_POST['item_id']))
		$a['item_id'] = (int)$_POST['item_id'];
	$db->insert('item', $a, TRUE, TRUE);
	if(isset($_POST['item_id']))
		$itemId = $_POST['item_id'];
	else
		$itemId = $db->autoIncreasementId;
	header('Location: item.php?id='.$itemId);
	exit(0);
}

if(isset($_GET['id'])) {
	$items = $db->filteredQuery('SELECT * FROM `item` WHERE `item_id`=%d', array($_GET['id']), FALSE, TRUE);
	if(!count($items)) exit(0);
	$item = $items[0];
} else {
	$item = array(
		'title' => '',
		'content' => '',
		'form' => "status: 处理状态 || select 已读|正在处理|已完成|已失败\nnote: 备注 || text"
	);
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>添加/更新内容项目 | 班务管理系统</title>
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
				<?php if(isset($item['item_id'])) { ?>
					<input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
				<?php } ?>
				<p><label for="form_title">标题</label> <input type="text" id="form_title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>"></p>
				<p><label for="form_content">内容（Markdown格式）</label><br><textarea id="form_content" name="content" rows="20"><?php echo htmlspecialchars($item['content']); ?></textarea></p>
				<p><label for="form_form">表格脚本</label><br><textarea id="form_form" name="form" rows="5"><?php echo htmlspecialchars($item['form']); ?></textarea></p>
				<p><input type="submit" value="提交"></p>
			</form>
		</div>
		<div class="footer">
			<hr>
			<p>用户：<?php echo $userId; ?> <a href="password.php">更改密码</a> <a href="login.php?logout=<?php echo $userId; ?>">登出</a> <?php if($manage) { ?>| <a href="user_management.php">管理用户</a> <?php } ?>| 班务管理系统 | Copyright 2013 LastLeaf</p>
		</div>
	</div>
</body>
</html>