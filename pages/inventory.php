<?php
$title = 'My Stuff';
$u = need_login();
$type = strtolower((string)($_GET['type'] ?? 'hat'));
$allowed = ['hat', 'face', 'shirt', 'pants', 'gear', 'other'];
if (!in_array($type, $allowed, true)) $type = 'hat';
$inventoryRows = rows(
    'SELECT i.* FROM inventory v INNER JOIN catalog_items i ON i.id=v.item_id
     WHERE v.user_id=? AND i.item_type=? ORDER BY v.acquired_at DESC LIMIT 100',
    [$u['id'], $type]
);
ob_start();
?>
<div id="MyRobloxContainer">
  <h2>My Stuff</h2>
  <div class="DisplayFilters">
    <h4>Categories</h4>
    <ul>
      <?php foreach (['hat'=>'Hats','face'=>'Faces','shirt'=>'Shirts','pants'=>'Pants','gear'=>'Gear','other'=>'Other'] as $key=>$label): ?>
        <li><a href="/Inventory.aspx?type=<?= e($key) ?>"><?= e($label) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div id="UserAssetsPane" class="Panel">
    <h4>My <?= e(ucfirst($type)) ?></h4>
    <div style="padding:8px">
      <?php foreach ($inventoryRows as $asset): ?>
        <div class="Asset" style="display:inline-block;vertical-align:top">
          <div class="AssetThumbnail">
            <a href="/Item.aspx?id=<?= (int)$asset['id'] ?>">
              <img src="<?= e($asset['image_path'] ?: '/images/placeholder.png') ?>" width="120" height="120" alt="<?= e($asset['name']) ?>" />
            </a>
          </div>
          <div class="AssetDetails"><a href="/Item.aspx?id=<?= (int)$asset['id'] ?>"><?= e($asset['name']) ?></a></div>
        </div>
      <?php endforeach; ?>
      <?php if (!$inventoryRows): ?><p>You do not have any items in this category yet.</p><?php endif; ?>
    </div>
  </div>
</div>
<?php
$body = ob_get_clean();
