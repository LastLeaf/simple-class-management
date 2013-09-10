<?php if(!defined('__DIRECT_REQUEST__')) exit(-1);

require_once('config.php');
require_once('LastLeaf/dbquery.php');
require_once('LastLeaf/randcrypt.php');

date_default_timezone_set('Asia/Shanghai');
session_start();

$db = new DbQuery($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME, $DB_PORT, TRUE);
$crypt = new RandCrypt();

$userId = '';
if(isset($_SESSION['userId'])) {
	$userId = $_SESSION['userId'];
	$r = $db->filteredQuery('SELECT `type` FROM `user` WHERE `user_id`=%s', array($userId), TRUE, TRUE);
	if(!count($r)) $userId = '';
	else $userType = $r[0]['type'];
}