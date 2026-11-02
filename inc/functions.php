<?php
function e(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function csrf():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function check_csrf():void{if(!hash_equals(csrf(),(string)($_POST['csrf']??''))){http_response_code(419);exit('Session expired. Reload the page.');}}
function go(string $url):never{header('Location: '.$url,true,303);exit;}
function notice(string $message,string $type='info'):void{$_SESSION['notice']=[$message,$type];}
function take_notice():?array{$n=$_SESSION['notice']??null;unset($_SESSION['notice']);return $n;}
function login_required():void{if(empty($_SESSION['user_id'])){notice('Sign in to continue.','error');go('/Login.aspx');}}

function moderator_required():void{login_required();if(!in_array($GLOBALS['currentUser']['role']??'user',['moderator','admin'],true)){http_response_code(403);exit('Moderator access required.');}}
function admin_required():void{login_required();if(($GLOBALS['currentUser']['role']??'user')!=='admin'){http_response_code(403);exit('Administrator access required.');}}
function record_currency(PDO $db,int $userId,string $currency,int $amount,string $reason,?string $refType=null,?int $refId=null):void{$q=$db->prepare('INSERT INTO currency_transactions(user_id,currency,amount,reason,reference_type,reference_id) VALUES(?,?,?,?,?,?)');$q->execute([$userId,$currency,$amount,$reason,$refType,$refId]);}
