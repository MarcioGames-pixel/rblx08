<?php
$topicId = max(0, (int)($_GET['id'] ?? $_GET['ID'] ?? 0));
$topic = $topicId ? one(
    'SELECT t.*,u.username FROM forum_topics t INNER JOIN users u ON u.id=t.user_id WHERE t.id=?',
    [$topicId]
) : null;
if ($topic && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = need_login();
    check_token();
    $message = trim((string)($_POST['body'] ?? ''));
    if ($message !== '' && db()) {
        db()->prepare('INSERT INTO forum_replies(topic_id,user_id,body) VALUES(?,?,?)')
            ->execute([$topicId, $u['id'], $message]);
        db()->prepare('UPDATE forum_topics SET replies_count=replies_count+1 WHERE id=?')->execute([$topicId]);
        go(route('Forum/ShowPost', ['id' => $topicId]));
    }
}
$title = $topic ? (string)$topic['title'] : 'Topic not found';
$replies = $topic ? rows(
    'SELECT r.*,u.username FROM forum_replies r INNER JOIN users u ON u.id=r.user_id
     WHERE r.topic_id=? ORDER BY r.id ASC',
    [$topicId]
) : [];
ob_start();
?>
<div id="ForumContainer">
  <h2><a href="/Forum/ShowForum.aspx">Forum</a></h2>
  <div class="Panel">
    <?php if ($topic): ?>
      <h4><?= e($topic['title']) ?></h4>
      <div style="padding:10px">
        <div class="ForumPost">
          <strong><?= e($topic['username']) ?></strong>
          <p><?= nl2br(e($topic['body'])) ?></p>
          <small><?= e($topic['created_at']) ?></small>
        </div>
        <h3>Replies (<?= count($replies) ?>)</h3>
        <?php foreach ($replies as $reply): ?>
          <div class="ForumPost" style="border-top:1px solid #ccc;padding:8px 0">
            <strong><?= e($reply['username']) ?></strong>
            <p><?= nl2br(e($reply['body'])) ?></p>
            <small><?= e($reply['created_at']) ?></small>
          </div>
        <?php endforeach; ?>
        <?php if (logged()): ?>
          <form method="post">
            <input type="hidden" name="_csrf" value="<?= e(token()) ?>" />
            <label>Reply<br /><textarea class="MultilineTextBox" name="body" required></textarea></label>
            <button class="Button" type="submit">Post Reply</button>
          </form>
        <?php else: ?>
          <p><a href="<?= e(route('Login')) ?>">Login</a> to reply.</p>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <h4>Topic not found</h4>
    <?php endif; ?>
  </div>
</div>
<?php
$body = ob_get_clean();
