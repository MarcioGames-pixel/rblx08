<?php
$flashes = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
$user = logged();
$siteName = $cfg['app_name'] ?? 'ROBLOX';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" id="www-roblox-com">
<head>
  <title><?= e($title) ?> - <?= e($siteName) ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link id="ctl00_Imports" rel="stylesheet" type="text/css" href="/AllCSSnew.css" />
  <link rel="shortcut icon" type="image/ico" href="/images/MADBLOX-Favicon.png" />
</head>
<body>
<div id="Container">
  <div id="Header">
    <div id="Banner">
      <div id="Options">
        <div id="Authentication">
          <?php if ($user): ?>
            Logged in as <?= e($user['username']) ?>&nbsp;<strong>|</strong>&nbsp;
            <a href="<?= e(route('User', ['ID' => $user['id']])) ?>">My Profile</a>&nbsp;<strong>|</strong>&nbsp;
            <form method="post" action="<?= e(route('Login')) ?>" style="display:inline">
              <input type="hidden" name="_csrf" value="<?= e(token()) ?>" />
              <input type="hidden" name="action" value="logout" />
              <button type="submit" class="HeaderLinkButton">Logout</button>
            </form>
          <?php else: ?>
            <a href="<?= e(route('Login')) ?>">Login</a>
          <?php endif; ?>
        </div>
        <div id="Settings">
          <?php if ($user): ?>Welcome to <?= e($siteName) ?><?php endif; ?>
        </div>
      </div>
      <div id="Logo">
        <a id="ctl00_rbxImage_Logo" title="ROBLOX" href="<?= e(route('Default')) ?>">
          <img src="/images/roblox_logo_2008.png" border="0" id="img" alt="ROBLOX" />
        </a>
      </div>
      <div id="Alerts">
        <table style="width:100%;height:100%"><tr><td valign="middle">
          <?php if (!$user): ?>
            <a id="ctl00_rbxAlerts_SignupAndPlayHyperLink" class="SignUpAndPlay" href="<?= e(route('Register')) ?>">
              <img src="/images/SignupBannerV2.png" alt="Sign-up and Play!" border="0" />
            </a>
          <?php else: ?>
            <div id="AlertSpace">
              <div><a href="<?= e(route('Inventory')) ?>">My Inventory</a></div>
              <div><a href="<?= e(route('User', ['ID' => $user['id']])) ?>">My Profile</a></div>
            </div>
          <?php endif; ?>
        </td></tr></table>
      </div>
    </div>
    <div class="Navigation">
      <?php if ($user): ?>
        <span><a class="MenuItem" href="<?= e(route('User', ['ID' => $user['id']])) ?>">My <?= e($siteName) ?></a></span>
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

  <?php foreach ($flashes as $flash): ?>
    <div class="SystemAlert"><div class="SystemAlertText"><?= e($flash[0] ?? '') ?></div></div>
  <?php endforeach; ?>

  <div id="Body"><?= $body ?></div>

  <div id="Footer">
    <hr />
    <p class="Legalese">
      <?= e($siteName) ?> is an independent community project and is not affiliated with Roblox Corporation.
      <br />Use of this site is subject to its community rules.
      <br />Classic site layout adapted from the Goodblox reference project.
    </p>
  </div>
</div>
</body>
</html>
