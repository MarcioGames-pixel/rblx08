<?php
$title=$page;
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_token();$name=trim((string)($_POST['username']??''));$pass=(string)($_POST['password']??'');
 if(($_POST['action']??'')==='logout'){$_SESSION=[];session_regenerate_id(true);say('Signed out.');go(route('Default'));}
 if($page==='Register'){
  $email=trim((string)($_POST['email']??''));
  if(!preg_match('/^[A-Za-z0-9_]{3,20}$/',$name)||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pass)<8)say('Enter a 3–20 character username, valid email and password of at least 8 characters.','warning');
  elseif(db()){try{$s=db()->prepare('INSERT INTO users(username,email,password_hash) VALUES(?,?,?)');$s->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT)]);session_regenerate_id(true);$_SESSION['user']=['id'=>(int)db()->lastInsertId(),'username'=>$name];say('Account created!');go(route('Default'));}catch(PDOException $e){say('Username or email may already exist.','warning');}}
  else say('Configure config.php and import database/schema.sql first.','warning');
 }elseif(db()&&($u=one('SELECT id,username,password_hash FROM users WHERE username=? OR email=? LIMIT 1',[$name,$name]))&&password_verify($pass,$u['password_hash'])){
  session_regenerate_id(true);$_SESSION['user']=['id'=>(int)$u['id'],'username'=>$u['username']];db()->prepare('UPDATE users SET last_login_at=NOW() WHERE id=?')->execute([$u['id']]);say('Welcome back!');go(route('Default'));
 }else say('Login failed. Check your details.','warning');
}
$user=logged();
if($user)$body='<section class="panel"><h1>Already signed in</h1></section>';
else{$body='<section class="auth panel"><h1>'.($page==='Register'?'Create an account':'Welcome back').'</h1><form method="post"><input type="hidden" name="_csrf" value="'.e(token()).'">'.($page==='Register'?'<label>Username<input name="username" minlength="3" maxlength="20" pattern="[A-Za-z0-9_]+" required></label><label>Email<input type="email" name="email" required></label>':'<label>Username or email<input name="username" required></label>').'<label>Password<input type="password" name="password" minlength="'.($page==='Register'?'8':'1').'" required></label><button>'.($page==='Register'?'Create account':'Login').'</button></form><p>'.($page==='Register'?'<a href="'.e(route('Login')).'">Login</a>':'<a href="'.e(route('Register')).'">Create account</a>').'</p></section>';}
