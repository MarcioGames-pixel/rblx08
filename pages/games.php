<?php
$title='Games';$body='<div class="page-heading"><h1>Games</h1><p>Explore places created by the community.</p></div><section class="content-box"><h2 class="section-title">Featured Games</h2><div class="cards">';
foreach(rows('SELECT g.*,u.username FROM games g LEFT JOIN users u ON u.id=g.creator_id ORDER BY g.id DESC LIMIT 60') as $g)$body.='<article class="card"><div class="thumb">PLAY</div><h3>'.e($g['name']).'</h3><p>'.e($g['description']??'').'</p></article>';
$body.='</div></section>';
