<?php
require __DIR__.'/inc/page_helpers.php';
$q=$db->prepare('SELECT c.*,u.username creator FROM catalog_items c LEFT JOIN users u ON u.id=c.creator_id WHERE c.id=?');
$q->execute([(int)($_GET['id']??0)]);$item=$q->fetch();
if($item&&page_post()){
 login_required();check_csrf();
 try{
  $db->beginTransaction();
  $q=$db->prepare('SELECT tickets FROM users WHERE id=? FOR UPDATE');$q->execute([$currentUser['id']]);$buyer=$q->fetch();
  $q=$db->prepare('SELECT * FROM catalog_items WHERE id=? FOR UPDATE');$q->execute([$item['id']]);$locked=$q->fetch();
  if(!$locked)throw new RuntimeException('Item no longer exists.');
  $q=$db->prepare('SELECT id FROM inventory WHERE user_id=? AND item_id=?');$q->execute([$currentUser['id'],$item['id']]);
  if($q->fetch())throw new RuntimeException('You already own this item.');
  if($locked['stock']!==null&&(int)$locked['stock']<1)throw new RuntimeException('This item is sold out.');
  $price=(int)$locked['price'];if((int)$buyer['tickets']<$price)throw new RuntimeException('Not enough Tickets.');
  $db->prepare('UPDATE users SET tickets=tickets-? WHERE id=?')->execute([$price,$currentUser['id']]);
  $db->prepare('INSERT INTO inventory(user_id,item_id) VALUES(?,?)')->execute([$currentUser['id'],$item['id']]);
  $db->prepare('UPDATE catalog_items SET sold_count=sold_count+1,stock=IF(stock IS NULL,NULL,stock-1) WHERE id=?')->execute([$item['id']]);
  record_currency($db,(int)$currentUser['id'],'tickets',-$price,'Catalog purchase','item',(int)$item['id']);
  if(!empty($locked['creator_id'])){$db->prepare('UPDATE users SET tickets=tickets+? WHERE id=?')->execute([$price,(int)$locked['creator_id']]);record_currency($db,(int)$locked['creator_id'],'tickets',$price,'Item sale','item',(int)$item['id']);}
  $db->commit();notice('Purchase complete.');go('/Item.php?id='.$item['id']);
 }catch(Throwable $e){if($db->inTransaction())$db->rollBack();notice($e instanceof RuntimeException?$e->getMessage():'Purchase could not be completed.','error');go('/Item.php?id='.$item['id']);}
}
page_start($item?$item['name']:'Item not found');
?>
<section class="Panel"><?php if(!$item):?><h1>Item not found</h1><?php else:?>
<h1><?=e($item['name'])?></h1><p><?=e($item['description'])?></p>
<p>Type: <?=e($item['type'])?> · Price: <?=(int)$item['price']?> Tickets</p>
<p>Creator: <?=e($item['creator']??'ROBLOX')?> · Sold: <?=(int)$item['sold_count']?></p>
<?php if($currentUser):?><p>Your balance: <?=(int)$currentUser['tickets']?> Tickets</p><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><button class="Button">Buy item</button></form><?php else:?><a href="/Login.php">Login to buy</a><?php endif;?>
<form method="post" action="/Report.php"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="type" value="item"><input type="hidden" name="id" value="<?=(int)$item['id']?>"><label>Report reason<input name="reason" maxlength="1000" required></label><button>Report item</button></form>
<?php endif;?></section><?php page_end();?>