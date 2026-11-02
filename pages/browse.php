<?php
$title='Browse';$body='<div class="page-heading"><h1>Browse</h1><p>Browse community items and creations.</p></div><section class="content-box"><h2 class="section-title">Catalog</h2><div class="cards">';
foreach(items((string)($_GET['type']??'')) as $i)$body.='<a class="card" href="'.e(route('Item',['id'=>$i['id']])).'"><div class="thumb">'.e(strtoupper(substr($i['item_type'],0,1))).'</div><h3>'.e($i['name']).'</h3><p>'.number_format((int)$i['price']).' tickets</p></a>';
$body.='</div></section>';
