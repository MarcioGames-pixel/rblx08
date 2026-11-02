<?php
$title = 'People';
$search = trim((string)($_GET['q'] ?? ''));
$pageNumber = max(1, min(100000, (int)($_GET['page'] ?? 1)));
$perPage = 10;
$where = '';
$params = [];
if ($search !== '') {
    $where = ' WHERE username LIKE ?';
    $params[] = '%' . $search . '%';
}
$countRow = one('SELECT COUNT(*) AS total FROM users' . $where, $params);
$totalUsers = (int)($countRow['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalUsers / $perPage));
$pageNumber = min($pageNumber, $totalPages);
$userRows = rows(
    'SELECT id, username, description, created_at FROM users' . $where .
    ' ORDER BY id DESC LIMIT ' . $perPage . ' OFFSET ' . (($pageNumber - 1) * $perPage),
    $params
);
ob_start();
?>
<div id="BrowseContainer" style="text-align:center">
  <form class="SearchBar" method="get" action="/Browse.aspx">
    <span class="SearchBox"><input type="text" name="q" maxlength="100" class="TextBox" value="<?= e($search) ?>" placeholder="Search people" /></span>
    <span class="SearchButton"><button type="submit">Search</button></span>
  </form>
  <table class="Grid" cellspacing="0" cellpadding="4" border="0" style="border-collapse:collapse;width:100%">
    <tbody>
      <tr class="GridHeader">
        <th scope="col">Avatar</th><th scope="col">Name</th><th scope="col">Status</th><th scope="col">Location / Joined</th>
      </tr>
      <?php foreach ($userRows as $person): ?>
        <tr class="GridItem">
          <td><a href="/User.aspx?ID=<?= (int)$person['id'] ?>"><img src="/images/AvatarPlaceholder.png" title="<?= e($person['username']) ?>" width="60" height="60" alt="" /></a></td>
          <td style="word-break:break-all;text-align:left">
            <a href="/User.aspx?ID=<?= (int)$person['id'] ?>"><?= e($person['username']) ?></a><br />
            <span><?= e($person['description'] ?? '') ?></span>
          </td>
          <td><span class="UserOfflineStatus">Offline</span></td>
          <td><?= e($person['created_at'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$userRows): ?><tr class="GridItem"><td colspan="4">No users found.</td></tr><?php endif; ?>
      <tr class="GridPager"><td colspan="4">
        <?php if ($pageNumber > 1): ?><a href="/Browse.aspx?page=<?= $pageNumber - 1 ?>&amp;q=<?= rawurlencode($search) ?>">&lt; Previous</a><?php endif; ?>
        &nbsp; Page <?= $pageNumber ?> of <?= $totalPages ?> &nbsp;
        <?php if ($pageNumber < $totalPages): ?><a href="/Browse.aspx?page=<?= $pageNumber + 1 ?>&amp;q=<?= rawurlencode($search) ?>">Next &gt;</a><?php endif; ?>
      </td></tr>
    </tbody>
  </table>
</div>
<?php
$body = ob_get_clean();
