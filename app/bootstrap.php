<?php
declare(strict_types=1);
$f=dirname(__DIR__).'/config.php';
$cfg=file_exists($f)?require $f:require dirname(__DIR__).'/config.example.php';
date_default_timezone_set($cfg['timezone']??'UTC');
session_name($cfg['session_name']??'rblx08_session');
if(session_status()!==PHP_SESSION_ACTIVE)session_start(['cookie_httponly'=>true,'cookie_samesite'=>'Lax','use_strict_mode'=>true]);
function e(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function route(string $p,array $q=[]):string{$r=['Default'=>'/Default.aspx','Games'=>'/Games.aspx','Browse'=>'/Browse.aspx','Catalog'=>'/Catalog.aspx','Item'=>'/Item.aspx','User'=>'/User.aspx','Inventory'=>'/Inventory.aspx','Login'=>'/Login.aspx','Register'=>'/Register.aspx','Forum/ShowForum'=>'/Forum/ShowForum.aspx','Forum/ShowPost'=>'/Forum/ShowPost.aspx'];return($r[$p]??'/').($q?'?'.http_build_query($q):'');}
function go(string $u):never{header('Location: '.$u,true,303);exit;}
function token():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function check_token():void{if(!hash_equals(token(),(string)($_POST['_csrf']??''))){http_response_code(419);exit('Invalid form token. Reload the page.');}}
function say(string $m,string $t='info'):void{$_SESSION['flash'][]=[$m,$t];}
function db():?PDO{static $p=false;if($p instanceof PDO)return$p;if($p===null)return null;$c=$GLOBALS['cfg']['database']??[];try{$p=new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',$c['host']??'127.0.0.1',(int)($c['port']??3306),$c['name']??'rblx08',$c['charset']??'utf8mb4'),$c['user']??'',$c['password']??'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);return$p;}catch(PDOException $e){error_log($e->getMessage());$p=null;return null;}}
function rows(string $sql,array $a=[]):array{if(!db())return[];$s=db()->prepare($sql);$s->execute($a);return$s->fetchAll();}
function one(string $sql,array $a=[]):?array{return rows($sql,$a)[0]??null;}
function logged():?array{return $_SESSION['user']??null;}
function need_login():array{if(!logged()){say('Please sign in first.','warning');go(route('Login'));}return logged();}
function items(string $type=''):array{$sql='SELECT i.*,u.username creator_name FROM catalog_items i LEFT JOIN users u ON u.id=i.creator_id';$a=[];if(in_array($type,['hat','face','shirt','pants','gear','other'],true)){$sql.=' WHERE i.item_type=?';$a[]=$type;}return rows($sql.' ORDER BY i.id DESC LIMIT 60',$a);}
