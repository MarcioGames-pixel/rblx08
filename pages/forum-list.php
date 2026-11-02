<?php
$title = 'Forum';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = need_login();
    check_token();
    $subject = trim((string)($_POST['title'] ?? ''));
    $message = trim((string)($_POST['body'] ?? ''));
    if ($subject !== '' && $message !== '' && db()) {
        db()->prepare('INSERT INTO forum_topics(user_id,title,body) VALUES(?,?,?)')
            ->execute([$u['id'], substr($subject, 0, 160), $message]);
        say('Topic posted.');
        go(route('Forum/ShowForum'));
    }
}
$topics = rows(
    'SELECT t.*,u.username FROM forum_topics t
     INNER JOIN users u ON u.id=t.user_id ORDER BY t.id DESC LIMIT 60'
);
ob_start();
?>
<div id="ForumContainer">
  <h2>Forum</h2>
  <div class="Panel">
    <h4>Community Forum</h4>
    <div style="padding:10px">
      <?php if (logged()): ?>
        <form method="post">
          <input type="hidden" name="_csrf" value="<?= e(token()) ?>" />
          <label>Subject<br /><input class="TextBox" name="title" required maxlength="160" /></label>
          <label>Message<br /><textarea class="MultilineTextBox" name="body" required></textarea></label>
          <button class="Button" type="submit">Create Topic</button>
        </form>
        <hr />
      <?php else: ?>
        <p><a href="<?= e(route('Login')) ?>">Login</a> to create a forum topic.</p>
      <?php endif; ?>
      <table class="Grid" width="100%" cellspacing="0" cellpadding="4">
        <tbody>
        <tr class="GridHeader"><th>Topic</th><th>Author</th><th>Replies</th><th>Posted</th></tr>
        <?php foreach ($topics as $topic): ?>
          <tr class="GridItem">
            <td><a href="/Forum/ShowPost.aspx?id=<?= (int)$topic['id'] ?>"><?= e($topic['title']) ?></a></td>
            <td><?= e($topic['username']) ?></td>
            <td><?= (int)$topic['replies_count'] ?></td>
            <td><?= e($topic['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$topics): ?><tr class="GridItem"><td colspan="4">No topics have been posted.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$body = ob_get_clean();
