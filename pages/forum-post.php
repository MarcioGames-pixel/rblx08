<?php
$title='Forum Topic';$t=one('SELECT t.*,u.username FROM forum_topics t JOIN users u ON u.id=t.user_id WHERE t.id=?',[(int)($_GET['id']??0)]);$body='<section class="panel">';
if($t){
 $body.='<h1>'.e($t['title']).'</h1><p>'.nl2br(e($t['body'])).'</p>';
 foreach(rows('SELECT r.*,u.username FROM forum_replies r JOIN users u ON u.id=r.user_id WHERE topic_id=? ORDER BY r.id',[$t['id']]) as $r)$body.='<article><b>'.e($r['username']).'</b><p>'.nl2br(e($r['body'])).'</p></article>';
 if(logged()&&$_SERVER['REQUEST_METHOD']==='POST'){need_login();check_token();$b=trim((string)($_POST['body']??''));if($b!==''){db()->prepare('INSERT INTO forum_replies(topic_id,user_id,body) VALUES(?,?,?)')->execute([$t['id'],logged()['id'],$b]);db()->prepare('UPDATE forum_topics SET replies_count=replies_count+1 WHERE id=?')->execute([$t['id']]);go(route('Forum/ShowPost',['id'=>$t['id']]));}}
 if(logged())$body.='<form method="post"><input type="hidden" name="_csrf" value="'.e(token()).'"><textarea name="body" required></textarea><button>Reply</button></form>';
}else $body.='<h1>Topic not found</h1>';
$body.='</section>';
