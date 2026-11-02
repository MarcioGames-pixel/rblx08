<?php
$title='Player Profile';$u=one('SELECT id,username,description,created_at FROM users WHERE id=?',[(int)($_GET['ID']??$_GET['id']??0)]);
$body=$u?'<section class="panel"><h1>'.e($u['username']).'</h1><p>'.nl2br(e($u['description']??'')).'</p><h2>Inventory</h2>':'<section class="panel"><h1>User not found</h1>';
if($u){foreach(rows('SELECT i.* FROM inventory v JOIN catalog_items i ON i.id=v.item_id WHERE v.user_id=?',[$u['id']]) as $i)$body.='<p><a href="'.e(route('Item',['id'=>$i['id']])).'">'.e($i['name']).'</a></p>';}
$body.='</section>';
