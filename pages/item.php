<?php
$itemId = max(0, (int)($_GET['id'] ?? $_GET['ID'] ?? 0));
$item = $itemId ? one(
    'SELECT i.*, u.username AS creator_name FROM catalog_items i
     LEFT JOIN users u ON u.id=i.creator_id WHERE i.id=?',
    [$itemId]
) : null;
if (!$item) {
    http_response_code(404);
    $title = 'Item not found';
    $body = '<div class="Panel"><h4>Item not found</h4><p>This catalog item does not exist.</p></div>';
} else {
    $title = (string)$item['name'];
    $owned = false;
    if (logged() && db()) {
        $owned = one('SELECT id FROM inventory WHERE user_id=? AND item_id=?', [logged()['id'], $itemId]) !== null;
    }
    ob_start();
    ?>
    <div id="ItemContainer">
      <h2><?= e($item['name']) ?></h2>
      <div id="Item">
        <div id="Thumbnail">
          <img src="<?= e($item['image_path'] ?: '/images/placeholder.png') ?>" width="250" height="250" border="0" alt="<?= e($item['name']) ?>" />
        </div>
        <div id="Summary">
          <h3><?= e(ucfirst($item['item_type'])) ?></h3>
          <div id="PriceInTickets">Tx: <?= number_format((int)$item['price']) ?></div>
          <br />
          <div id="Creator">
            <img src="/images/AvatarPlaceholder.png" width="64" height="64" alt="" /><br />
            <span style="color:#555">Creator: </span>
            <?php if (!empty($item['creator_id'])): ?>
              <a href="/User.aspx?ID=<?= (int)$item['creator_id'] ?>"><?= e($item['creator_name'] ?? 'Unknown') ?></a>
            <?php else: ?>
              <?= e($item['creator_name'] ?? 'ROBLOX') ?>
            <?php endif; ?>
          </div>
          <div id="DescriptionLabel">Description:</div>
          <div id="Description"><?= nl2br(e($item['description'] ?? '')) ?></div>
          <?php if ($owned): ?>
            <p>You already own this item.</p>
          <?php elseif (logged()): ?>
            <p>Purchases are not enabled yet.</p>
          <?php else: ?>
            <p><a class="Button" href="<?= e(route('Login')) ?>">Login to get this item</a></p>
          <?php endif; ?>
        </div>
        <div style="clear:both"></div>
      </div>
    </div>
    <?php
    $body = ob_get_clean();
}
