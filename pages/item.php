<?php
$title='Catalog Item';$i=one('SELECT * FROM catalog_items WHERE id=?',[(int)($_GET['id']??0)]);
$body=$i?'<section class="panel"><h1>'.e($i['name']).'</h1><p>'.e($i['description']??'').'</p><b>'.number_format((int)$i['price']).' tickets</b></section>':'<section class="panel"><h1>Item not found</h1></section>';
