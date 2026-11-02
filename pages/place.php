<?php
$gameId = max(0, (int)($_GET['ID'] ?? $_GET['id'] ?? 0));
$game = $gameId ? one(
    'SELECT g.*, u.username AS creator_name FROM games g LEFT JOIN users u ON u.id=g.creator_id WHERE g.id=?',
    [$gameId]
) : null;
if (!$game) {
    http_response_code(404);
    $title = 'Place not found';
    $body = '<div class="Panel"><h4>Place not found</h4><p>This game does not exist yet.</p></div>';
} else {
    $title = (string)$game['name'];
    ob_start();
    ?>
    <div id="PlaceContainer">
      <div class="Panel">
        <h4><?= e($game['name']) ?></h4>
        <div style="padding:12px">
          <img src="<?= e($game['thumbnail_path'] ?: '/images/PlacePlaceholder.png') ?>" width="160" height="100" alt="<?= e($game['name']) ?>" style="float:left;margin:0 12px 8px 0" />
          <p><?= nl2br(e($game['description'] ?? '')) ?></p>
          <p><strong>Creator:</strong> <?= e($game['creator_name'] ?? 'ROBLOX') ?></p>
          <p><strong>Visits:</strong> <?= number_format((int)$game['visits']) ?></p>
          <div style="clear:both"></div>
        </div>
      </div>
    </div>
    <?php
    $body = ob_get_clean();
    if (db()) db()->prepare('UPDATE games SET visits=visits+1 WHERE id=?')->execute([$gameId]);
}
