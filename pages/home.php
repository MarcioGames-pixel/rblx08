<?php
$title='Home';
$body='<div class="page-heading"><h1>Home</h1><p>Build, battle, chat, or just hang out.</p></div>';
$body.='<section class="content-box"><h2 class="section-title">Featured Games</h2><div class="cards">';
foreach(rows('SELECT g.*,u.username FROM games g LEFT JOIN users u ON u.id=g.creator_id ORDER BY g.id DESC LIMIT 30') as $g)$body.='<article class="card"><div class="thumb">PLAY</div><h3>'.e($g['name']).'</h3><p>'.e($g['description']??'').'</p></article>';
$body.='</div></section><section class="content-box"><h2 class="section-title">Catalog</h2><div class="cards">';
foreach(items() as $i)$body.='<a class="card" href="'.e(route('Item',['id'=>$i['id']])).'"><div class="thumb">'.e(strtoupper(substr($i['item_type'],0,1))).'</div><h3>'.e($i['name']).'</h3><p>'.number_format((int)$i['price']).' tickets</p></a>';
$body.='</div></section>';
