<?php
$title = 'Games - Most Popular';
$gameRows = rows(
    'SELECT g.*, u.username AS creator_name
     FROM games g LEFT JOIN users u ON u.id = g.creator_id
     ORDER BY g.visits DESC, g.id DESC LIMIT 40'
);
ob_start();
?>
<div id="GamesContainer">
  <div id="ctl00_cphRoblox_rbxGames_GamesContainerPanel">
    <div class="DisplayFilters">
      <h2>Games</h2>
      <div id="BrowseMode">
        <h4>Browse</h4>
        <ul>
          <li><a href="<?= e(route('Games')) ?>">Most Popular</a></li>
          <li><a href="<?= e(route('Games', ['sort' => 'new'])) ?>">Recently Updated</a></li>
          <li><a href="<?= e(route('Games', ['sort' => 'new'])) ?>">New Games</a></li>
        </ul>
      </div>
      <div id="Timespan">
        <h4>Time</h4>
        <ul>
          <li><a href="<?= e(route('Games')) ?>">Now</a></li>
          <li><a href="<?= e(route('Games', ['sort' => 'new'])) ?>">All-time</a></li>
        </ul>
      </div>
    </div>
    <div id="Games">
      <span class="GamesDisplaySet">Most Popular (Now)</span>
      <div class="HeaderPager"><span>Page 1 of 1</span></div>
      <table id="ctl00_cphRoblox_rbxGames_dlGames" cellspacing="0" align="left" border="0" width="550">
        <tbody>
        <?php foreach ($gameRows as $game): ?>
          <tr><td class="Game" valign="top">
            <div style="display:inline-block;cursor:pointer;">
              <div class="GameThumbnail">
                <a title="<?= e($game['name']) ?>" href="/PlaceItem.aspx?ID=<?= (int)$game['id'] ?>">
                  <img src="<?= e($game['thumbnail_path'] ?: '/images/PlacePlaceholder.png') ?>" width="160" height="100" border="0" alt="<?= e($game['name']) ?>" />
                </a>
              </div>
              <div class="GameDetails">
                <div class="GameName"><a href="/PlaceItem.aspx?ID=<?= (int)$game['id'] ?>"><?= e($game['name']) ?></a></div>
                <div class="GameLastUpdate"><span class="Label">Updated:</span> <span class="Detail"><?= e($game['created_at'] ?? 'Recently') ?></span></div>
                <div class="GameCreator"><span class="Label">Creator:</span> <span class="Detail"><?= e($game['creator_name'] ?? 'ROBLOX') ?></span></div>
                <div class="GamePlays"><span class="Label">Played:</span> <span class="Detail"><?= number_format((int)$game['visits']) ?> times</span></div>
              </div>
            </div>
          </td></tr>
        <?php endforeach; ?>
        <?php if (!$gameRows): ?>
          <tr><td class="Game">No games have been published yet.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div style="clear:both;"></div>
  </div>
</div>
<?php
$body = ob_get_clean();
