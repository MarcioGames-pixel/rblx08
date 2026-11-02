<?php
$title = 'Home';
ob_start();
?>
<div id="SplashContainer">
  <div id="SignInPane">
    <div id="LoginViewContainer">
      <div id="LoginView">
        <?php if ($user): ?>
          <h5>Logged In</h5>
          <div id="AlreadySignedIn">
            <a title="<?= e($user['username']) ?>" href="<?= e(route('User', ['ID' => $user['id']])) ?>" style="display:inline-block;height:190px;width:152px;cursor:pointer;">
              <img src="/images/FigureDefault.png" style="display:inline-block;width:145px;margin-top:15px;" border="0" alt="<?= e($user['username']) ?>" />
            </a>
            <p><a href="<?= e(route('Inventory')) ?>">View My Stuff</a></p>
          </div>
        <?php else: ?>
          <h5>Member Login</h5>
          <div class="AspNet-Login">
            <form method="post" action="<?= e(route('Login')) ?>">
              <input type="hidden" name="_csrf" value="<?= e(token()) ?>" />
              <div class="AspNet-Login-UserPanel">
                <label class="Label" for="username">Character Name</label>
                <input name="username" type="text" id="username" tabindex="1" class="Text" required />
              </div>
              <div class="AspNet-Login-PasswordPanel">
                <label class="Label" for="password">Password</label>
                <input name="password" type="password" id="password" tabindex="2" class="Text" required />
              </div>
              <div class="AspNet-Login-SubmitPanel">
                <button type="submit" tabindex="4" class="Button">Login</button>
              </div>
              <div class="AspNet-Login-SubmitPanel">
                <a tabindex="4" class="Button" href="<?= e(route('Register')) ?>">Register</a>
              </div>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <br />
    <?php if (!$user): ?>
      <div id="ctl00_cphRoblox_LoginView1_pFigure">
        <div id="Figure">
          <img src="/images/NewFrontPageGuy.png" alt="Figure" border="0" />
        </div>
      </div>
    <?php else: ?>
      <div style="text-align:center;background-color:#eeeeee;border:1px solid black;">
        <br /><h3><?= e($siteName) ?> News</h3>
        <a href="<?= e(route('Default')) ?>">Welcome to the revival!</a><br /><br />
        <a href="<?= e(route('Catalog')) ?>">Browse the catalog</a><br /><br />
      </div>
    <?php endif; ?>
  </div>

  <div id="RobloxAtAGlance">
    <h2><?= e($siteName) ?> Virtual Playworld</h2>
    <h3><?= e($siteName) ?> is Free!</h3>
    <ul id="ThingsToDo">
      <li id="Point1">
        <h3>Build your personal Place</h3>
        <div>Create buildings, vehicles, scenery, and traps with thousands of virtual bricks.</div>
      </li>
      <li id="Point2">
        <h3>Meet new friends online</h3>
        <div>Visit your friend's place, chat in 3D, and build together.</div>
      </li>
      <li id="Point3">
        <h3>Battle in the Brick Arenas</h3>
        <div>Explore classic games and play with friends in a blocky world.</div>
      </li>
    </ul>
    <div id="Showcase">
      <iframe width="400" height="326" src="https://www.bitview.net/embed.php?v=LYkKRQuT7_5" frameborder="0" allowfullscreen=""></iframe>
      <br /><br />
    </div>
    <div id="Install">
      <div id="CompatibilityNote"><div id="ctl00_cphRoblox_pCompatibilityNote">Works with your<br />Windows PC!</div></div>
      <div id="DownloadAndPlay">
        <a id="ctl00_cphRoblox_hlDownloadAndPlay" href="<?= e(route('Games')) ?>">
          <img src="/images/PlayNowGreenFader.gif" alt="FREE - Explore Games!" border="0" />
        </a>
      </div>
    </div>
    <div id="ctl00_cphRoblox_pForParents">
      <div id="ForParents">
        <img title="<?= e($siteName) ?>" src="/images/GamerSeal.png" border="0" alt="" />
      </div>
    </div>
  </div>

  <div id="UserPlacesPane">
    <div id="UserPlaces_Content">
      <table width="100%" cellspacing="0" border="0">
        <tbody><tr>
          <td class="UserPlace"><a title="Games" href="<?= e(route('Games')) ?>"><img src="/images/games_bullet.png" alt="Games" width="75" border="0" /></a></td>
          <td class="UserPlace"><a title="Catalog" href="<?= e(route('Catalog')) ?>"><img src="/images/DownloadAndPlay.png" alt="Catalog" width="75" border="0" /></a></td>
          <td class="UserPlace"><a title="People" href="<?= e(route('Browse')) ?>"><img src="/images/player.png" alt="People" width="75" border="0" /></a></td>
          <td class="UserPlace"><a title="Community" href="<?= e(route('Forum/ShowForum')) ?>"><img src="/images/discord.png" alt="Community" width="75" border="0" /></a></td>
        </tr></tbody>
      </table>
    </div>
    <div id="UserPlaces_Header" style="min-height:70px;">
      <h3><?= e($siteName) ?> Links</h3>
      <p>Check out the games, catalog, people and community!</p>
    </div>
  </div>
</div>
<?php
$body = ob_get_clean();
