<?php
declare(strict_types=1);
if(session_status()!==PHP_SESSION_ACTIVE){session_name('rblx08');session_start();}
$config=is_file(__DIR__.'/config.php')?require __DIR__.'/config.php':require __DIR__.'/config.example.php';
$dsn="mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset=utf8mb4";
try{$db=new PDO($dsn,$config['db_user'],$config['db_pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}
catch(PDOException $e){http_response_code(503);exit('MariaDB unavailable. Configure inc/config.php and import database/schema.sql.');}
require_once __DIR__.'/functions.php';
$currentUser=null;
if(!empty($_SESSION['user_id'])){$q=$db->prepare('SELECT id,username,blurb,created_at,last_seen,tickets,robux,role,account_status FROM users WHERE id=?');$q->execute([(int)$_SESSION['user_id']]);$currentUser=$q->fetch()?:null;if($currentUser && $currentUser['account_status']!=='active'){$_SESSION=[];session_destroy();$currentUser=null;}elseif($currentUser){$db->prepare('UPDATE users SET last_seen=NOW() WHERE id=?')->execute([$currentUser['id']]);}else unset($_SESSION['user_id']);}
