<?php
$type = strtolower((string)($_GET['type'] ?? 'hat'));
$categories = [
    'head' => ['Heads', 'other'],
    'face' => ['Faces', 'face'],
    'hat' => ['Hats', 'hat'],
    'shirt' => ['Shirts', 'shirt'],
    'pants' => ['Pants', 'pants'],
    'tshirt' => ['T-Shirts', 'other'],
    'gear' => ['Gear', 'gear'],
];
if (!isset($categories[$type])) $type = 'hat';
[$categoryName, $dbType] = $categories[$type];
$title = 'Catalog - ' . $categoryName;
$search = trim((string)($_GET['q'] ?? ''));
if ($search !== '') {
    $assetRows = rows(
        'SELECT i.*, u.username AS creator_name FROM catalog_items i
         LEFT JOIN users u ON u.id=i.creator_id
         WHERE i.item_type=? AND (i.name LIKE ? OR i.description LIKE ?)
         ORDER BY i.id DESC LIMIT 60',
        [$dbType, '%' . $search . '%', '%' . $search . '%']
    );
} else {
    $assetRows = rows(
        'SELECT i.*, u.username AS creator_name FROM catalog_items i
         LEFT JOIN users u ON u.id=i.creator_id
         WHERE i.item_type=? ORDER BY i.id DESC LIMIT 60',
        [$dbType]
    );
}
ob_start();
?>
<div id="CatalogContainer" style="margin-top:5px">
  <form id="SearchBar" class="SearchBar" method="get" action="/Catalog.aspx">
    <input type="hidden" name="type" value="<?= e($type) ?>" />
    <span class="SearchBox"><input name="q" type="text" maxlength="100" class="TextBox" value="<?= e($search) ?>" /></span>
    <span class="SearchButton"><button type="submit">Search</button></span>
  </form>
  <div class="DisplayFilters">
    <h2>Catalog</h2>
    <div id="BrowseMode">
      <h4>Browse</h4>
      <ul>
        <li><a href="/Catalog.aspx?type=hat">Featured</a></li>
        <li><a href="/Catalog.aspx?type=hat">For Sale</a></li>
        <li><a href="/Catalog.aspx?type=hat">Recently Updated</a></li>
      </ul>
    </div>
    <div id="Category">
      <h4>Category</h4>
      <ul>
        <?php foreach ($categories as $key => [$label, $unused]): ?>
          <li>
            <?php if ($type === $key): ?><img class="GamesBullet" src="/images/games_bullet.png" border="0" alt="" /><?php endif; ?>
            <a href="/Catalog.aspx?type=<?= e($key) ?>"><?= e($label) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="Assets">
    <span class="AssetsDisplaySet"><?= e($categoryName) ?></span>
    <div class="HeaderPager"><span>Page 1 of 1</span></div>
    <table id="ctl00_cphRoblox_rbxCatalog_AssetsDataList" cellspacing="0" align="center" border="0" width="735">
      <tbody><tr>
      <?php foreach ($assetRows as $item): ?>
        <td valign="top" style="display:inline-block;cursor:pointer;">
          <div class="Asset">
            <div class="AssetThumbnail">
              <a title="<?= e($item['name']) ?>" href="/Item.aspx?id=<?= (int)$item['id'] ?>">
                <img src="<?= e($item['image_path'] ?: '/images/placeholder.png') ?>" width="120" height="120" border="0" alt="<?= e($item['name']) ?>" />
              </a>
            </div>
            <div class="AssetDetails">
              <strong><a href="/Item.aspx?id=<?= (int)$item['id'] ?>"><?= e($item['name']) ?></a></strong>
              <div class="AssetCreator"><span class="Label">Creator:</span> <span class="Detail"><?= e($item['creator_name'] ?? 'ROBLOX') ?></span></div>
              <div class="AssetPrice"><span class="PriceInTickets">Tx: <?= number_format((int)$item['price']) ?></span></div>
            </div>
          </div>
        </td>
      <?php endforeach; ?>
      </tr></tbody>
    </table>
    <?php if (!$assetRows): ?><p>No catalog items found in this category.</p><?php endif; ?>
    <div class="HeaderPager"><span>Page 1 of 1</span></div>
  </div>
  <div style="clear:both;"></div>
</div>
<?php
$body = ob_get_clean();
