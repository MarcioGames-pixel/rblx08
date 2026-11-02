<?php
$title='Forum';
if($_SERVER['REQUEST_METHOD']==='POST'){$u=need_login();check_token();$s=trim((string)($_POST['title']??''));$b=trim((string)($_POST['body']??''));if($s!==''&&$b!==''&&db()){db()->prepare('INSERT INTO forum_topics(user_id,title,body) VALUES(?,?,?)')->execute([$u['id'],substr($s,0,160),$b]);say('Topic posted.');go(route('Forum/ShowForum'));}}
$body='<section class="panel"><h1>Community Forum</h1>';
if(logged())$body.='<form method="post"><input type="hidden" name="_csrf" value="'.e(token()).'"><label>Title<input name="title" required maxlength="160"></label><label>Message<textarea name="body" required></textarea></label><button>Post topic</button></form>';
foreach(rows('SELECT t.*,u.username FROM forum_topics t JOIN users u ON u.id=t.user_id ORDER BY t.id DESC LIMIT 60') as $t)$body.='<p><a href="'.e(route('Forum/ShowPost',['id'=>$t['id']])).'">'.e($t['title']).'</a> — '.e($t['username']).'</p>';
$body.='</section>';
