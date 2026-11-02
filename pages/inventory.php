<?php
$title='My Inventory';$u=need_login();$body='<section class="panel"><h1>My Inventory</h1>';
foreach(rows('SELECT i.* FROM inventory v JOIN catalog_items i ON i.id=v.item_id WHERE v.user_id=?',[$u['id']]) as $i)$body.='<p><a href="'.e(route('Item',['id'=>$i['id']])).'">'.e($i['name']).'</a></p>';
$body.='</section>';
