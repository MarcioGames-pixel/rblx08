<?php
$userId = max(0, (int)($_GET['ID'] ?? $_GET['id'] ?? 0));
$profile = $userId ? one('SELECT id, username, description, created_at, last_login_at FROM users WHERE id=?', [$userId]) : null;
if (!$profile) {
    http_response_code(404);
    $title = 'User not found';
    $body = '<div class="Panel"><h4>User not found</h4><p>This account could not be found.</p></div>';
} else {
    $title = $profile['username'] . "'s Home Page";
    $inventoryRows = rows(
        'SELECT i.id, i.name, i.item_type, i.image_path
         FROM inventory v INNER JOIN catalog_items i ON i.id=v.item_id
         WHERE v.user_id=? ORDER BY v.acquired_at DESC LIMIT 24',
        [$userId]
    );
    ob_start();
    ?>
    <div id="UserContainer">
      <div id="LeftBank">
        <div id="ProfilePane">
          <table width="100%" bgcolor="lightsteelblue" cellpadding="6" cellspacing="0">
            <tbody><tr><td>
              <span class="Title"><?= e($profile['username']) ?></span><br />
              <span class="UserOfflineMessage">[ Offline ]</span>
            </td></tr>
            <tr><td>
              <span><?= e($profile['username']) ?>'s <?= e($cfg['app_name'] ?? 'ROBLOX') ?>:</span><br />
              <a href="/User.aspx?ID=<?= (int)$profile['id'] ?>">/User.aspx?ID=<?= (int)$profile['id'] ?></a><br /><br />
              <img src="/images/AvatarPlaceholder.png" width="150" height="150" alt="<?= e($profile['username']) ?>" />
            </td></tr></tbody>
          </table>
        </div>
        <div id="Description">
          <h4>Description</h4>
          <div><?= nl2br(e($profile['description'] ?? '')) ?></div>
        </div>
      </div>
      <div id="RightBank">
        <div id="FriendsPane" class="Panel">
          <h4>Statistics</h4>
          <div style="padding:8px">
            <p><strong>Joined:</strong> <?= e($profile['created_at'] ?? '') ?></p>
            <p><strong>Last login:</strong> <?= e($profile['last_login_at'] ?? 'Never') ?></p>
            <p><strong>Items:</strong> <?= count($inventoryRows) ?></p>
          </div>
        </div>
        <div id="UserAssetsPane" class="Panel">
          <h4><?= e($profile['username']) ?>'s Stuff</h4>
          <div style="padding:8px">
            <?php foreach ($inventoryRows as $asset): ?>
              <div class="Asset" style="display:inline-block;vertical-align:top">
                <div class="AssetThumbnail">
                  <a href="/Item.aspx?id=<?= (int)$asset['id'] ?>">
                    <img src="<?= e($asset['image_path'] ?: '/images/placeholder.png') ?>" width="80" height="80" alt="<?= e($asset['name']) ?>" />
                  </a>
                </div>
                <div class="AssetDetails"><a href="/Item.aspx?id=<?= (int)$asset['id'] ?>"><?= e($asset['name']) ?></a></div>
              </div>
            <?php endforeach; ?>
            <?php if (!$inventoryRows): ?><p>This user has no items in their inventory yet.</p><?php endif; ?>
          </div>
        </div>
      </div>
      <div style="clear:both"></div>
    </div>
    <?php
    $body = ob_get_clean();
}
