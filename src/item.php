<?php define('__DIRECT_REQUEST__', ''); require('init.php');

if(!$userId) {
	header('Location: login.php');
	exit(0);
}
$manage = FALSE;
if($userType === 'admin' || $userType === 'system')
	$manage = TRUE;

if(isset($_GET['id'])) {
	$items = $db->filteredQuery('SELECT * FROM `item` WHERE `item_id`=%d', array($_GET['id']), FALSE, TRUE);
	if(!count($items)) exit(0);
	$item = $items[0];
} else {
	header('Location: index.php');
	exit(0);
}

if(isset($_POST['iu'])) {
	$is = explode("\n", $item['form']);
	$r = array();
	foreach($is as $i) {
		if(!preg_match('/^([0-9a-z_]+):/i', $i, $a))
			continue;
		$t = 'iu_'.$a[1];
		if(isset($_POST[$t]))
			$r[$a[1]] = $_POST[$t];
	}
	$db->insert('item_user', array(
		'item_id' => (int)$_GET['id'],
		'user_id' => $userId,
		'user_form' => serialize($r),
		'user_time' => time()
	), TRUE, TRUE);
	header('Location: index.php');
	exit(0);
}

$r = $db->filteredQuery('SELECT `user_form` FROM `item_user` WHERE `item_id`=%d AND `user_id`=%s', array(
	(int)$_GET['id'], $userId
), TRUE, TRUE);
if(count($r))
	$userForm = unserialize($r[0]['user_form']);
else
	$userForm = array();

require_once('Michelf/Markdown.php');
$content = Markdown::defaultTransform(htmlspecialchars($item['content']));

$is = explode("\n", $item['form']);
$formItems = array();
foreach($is as $i) {
	if(!preg_match('/^([0-9a-z_]+):([^\|]+)\|([^\|]*)\|([^\r\n]+)/i', $i, $a))
		continue;
	$id = $a[1];
	$f = array('id'=>$id, 'name'=>trim($a[2]), 'description'=>trim($a[3]));
	$fop = trim($a[4]);
	if(preg_match('/^select[ \t]+(.+)$/', $fop, $b)) {
		if(!isset($userForm[$id])) $userForm[$id] = '';
		$f['html'] = '<label for="form_'.$id.'">'.htmlspecialchars($f['name']).'</label> <select id="form_'.$id.'" name="iu_'.$id.'">';
		foreach(explode('|', $b[1]) as $t) {
			if($userForm[$id] === trim($t))
				$sel = ' selected';
			else
				$sel = '';
			$f['html'] .= '<option value="'.htmlspecialchars(trim($t)).'"'.$sel.'>'.htmlspecialchars(trim($t)).'</option>';
		}
		$f['html'] .= '</select>';
	} else if(preg_match('/^text($|[ \t].*$)/', $fop, $b)) {
		if(!isset($userForm[$id])) $userForm[$id] = trim($b[1]);
		$f['html'] = '<label for="form_'.$id.'">'.htmlspecialchars($f['name']).'</label> <input type="text" id="form_'.$id.'" name="iu_'.$id.'" value="'.htmlspecialchars($userForm[$id]).'">';
	} else if(preg_match('/^hint[ \t$]/', $fop)) {
		$f['html'] = '<p>'.htmlspecialchars($f['name']).'</p>';
	} else
		continue;
	$formItems[] = $f;
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo htmlspecialchars($item['title']); ?> | 班务管理系统</title>
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
			<h1><?php echo htmlspecialchars($item['title']); ?></h1>
			<div class="item_content"><?php echo $content; ?></div>
			<hr>
			<form method="POST" action="" class="item_form">
				<?php foreach($formItems as $v) echo '<p>'.$v['html'].'</p>'; ?>
				<p><input type="hidden" name="iu" value="iu"></p>
				<p><label>&nbsp;</label> <input type="submit" value="确认"></p>
			</form>
			<?php if($manage) { ?>
				<hr>
				<p>管理操作：
					<a href="edit_item.php?id=<?php echo $_GET['id']; ?>">编辑</a>
					<a href="delete_item.php?id=<?php echo $_GET['id']; ?>">删除</a>
					<a href="stat_item.php?id=<?php echo $_GET['id']; ?>">统计结果</a>
				</p>
			<?php } ?>
		</div>
		<div class="footer">
			<hr>
			<p>用户：<?php echo $userId; ?> <a href="password.php">更改密码</a> <a href="login.php?logout=<?php echo $userId; ?>">登出</a> <?php if($manage) { ?>| <a href="user_management.php">管理用户</a> <?php } ?>| 班务管理系统 | Copyright 2013 LastLeaf</p>
		</div>
	</div>
</body>
</html>