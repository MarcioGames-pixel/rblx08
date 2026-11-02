<?php
$flashes = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
$user = logged();
$siteName = $cfg['app_name'] ?? 'ROBLOX';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e($title) ?> - <?= e($siteName) ?></title>
  <link rel="stylesheet" type="text/css" href="/assets/css/site.css" />
</head>
<body>
<div id="Container">
  <div id="Header">
    <div id="Banner">
      <div id="Options">
        <div id="Authentication">
          <?php if ($user): ?>
            Logged in as <?= e($user['username']) ?>
            <strong>|</strong>
            <a href="<?= e(route('User', ['ID' => $user['id']])) ?>">My Profile</a>
            <strong>|</strong>
            <form class="InlineForm" method="post" action="<?= e(route('Login')) ?>">
              <input type="hidden" name="_csrf" value="<?= e(token()) ?>" />
              <input type="hidden" name="action" value="logout" />
              <button class="TextButton" type="submit">Logout</button>
            </form>
          <?php else: ?>
            <a href="<?= e(route('Login')) ?>">Login</a>
            <strong>|</strong>
            <a href="<?= e(route('Register')) ?>">Register</a>
          <?php endif; ?>
        </div>
        <div id="Settings">
          <?php if ($user): ?>
            <a href="<?= e(route('Inventory')) ?>">My Inventory</a>
          <?php else: ?>
            A free online building community
          <?php endif; ?>
        </div>
      </div>

      <div id="Logo">
        <a href="<?= e(route('Default')) ?>" title="<?= e($siteName) ?>">
          <span class="BrandWord">ROBLOX</span>
        </a>
      </div>

      <div id="Alerts">
        <?php if ($user): ?>
          <div id="AlertSpace">
            <div class="AlertLine">Welcome back, <?= e($user['username']) ?>!</div>
            <div class="AlertLine"><a href="<?= e(route('Inventory')) ?>">View your inventory</a></div>
          </div>
        <?php else: ?>
          <div id="SignupSpace">
            <a class="SignUpAndPlay" href="<?= e(route('Register')) ?>">Sign Up and Play!</a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="Navigation">
      <?php if ($user): ?>
        <span><a class="MenuItem" href="<?= e(route('User', ['ID' => $user['id']])) ?>">My ROBLOX</a></span>
        <span class="Separator">&nbsp;|&nbsp;</span>
      <?php endif; ?>
      <span><a class="MenuItem" href="<?= e(route('Games')) ?>">Games</a></span>
      <span class="Separator">&nbsp;|&nbsp;</span>
      <span><a class="MenuItem" href="<?= e(route('Catalog')) ?>">Catalog</a></span>
      <span class="Separator">&nbsp;|&nbsp;</span>
      <span><a class="MenuItem" href="<?= e(route('Browse')) ?>">People</a></span>
      <span class="Separator">&nbsp;|&nbsp;</span>
      <span><a class="MenuItem" href="<?= e(route('Forum/ShowForum')) ?>">Forum</a></span>
    </div>
  </div>

  <div id="Body">
    <?php foreach ($flashes as $flash): ?>
      <div class="SystemAlert">
        <div class="SystemAlertText"><?= e($flash[0] ?? '') ?></div>
      </div>
    <?php endforeach; ?>
    <?= $body ?>
  </div>

  <div id="Footer">
    <hr />
    <p class="Legalese">
      ROBLOX 2008 Revival is an independent fan project and is not affiliated with Roblox Corporation.
      <br />
      This project is provided for historical preservation and educational purposes.
    </p>
  </div>
</div>
</body>
</html>
