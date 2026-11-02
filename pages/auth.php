<?php
$title = $page;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_token();
    $name = trim((string)($_POST['username'] ?? ''));
    $pass = (string)($_POST['password'] ?? '');
    if (($_POST['action'] ?? '') === 'logout') {
        $_SESSION = [];
        session_regenerate_id(true);
        say('Signed out.');
        go(route('Default'));
    }
    if ($page === 'Register') {
        $email = trim((string)($_POST['email'] ?? ''));
        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8) {
            say('Enter a 3–20 character username, valid email and password of at least 8 characters.', 'warning');
        } elseif (db()) {
            try {
                $s = db()->prepare('INSERT INTO users(username,email,password_hash) VALUES(?,?,?)');
                $s->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => (int)db()->lastInsertId(), 'username' => $name];
                say('Account created!');
                go(route('Default'));
            } catch (PDOException $e) {
                say('Username or email may already exist.', 'warning');
            }
        } else {
            say('Configure config.php and import database/schema.sql first.', 'warning');
        }
    } elseif (db() && ($u = one(
        'SELECT id,username,password_hash FROM users WHERE username=? OR email=? LIMIT 1',
        [$name, $name]
    )) && password_verify($pass, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$u['id'], 'username' => $u['username']];
        db()->prepare('UPDATE users SET last_login_at=NOW() WHERE id=?')->execute([$u['id']]);
        say('Welcome back!');
        go(route('Default'));
    } else {
        say('Login failed. Check your details.', 'warning');
    }
}
$user = logged();
ob_start();
?>
<div id="SplashContainer">
  <div id="SignInPane">
    <div id="LoginViewContainer">
      <div id="LoginView">
        <?php if ($user): ?>
          <h5>Logged In</h5>
          <div id="AlreadySignedIn">
            <a href="<?= e(route('User', ['ID' => $user['id']])) ?>"><?= e($user['username']) ?></a>
          </div>
        <?php else: ?>
          <h5><?= $page === 'Register' ? 'Create an Account' : 'Member Login' ?></h5>
          <div class="AspNet-Login">
            <form method="post">
              <input type="hidden" name="_csrf" value="<?= e(token()) ?>" />
              <?php if ($page === 'Register'): ?>
                <div class="AspNet-Login-UserPanel">
                  <label class="Label" for="username">Character Name</label>
                  <input class="Text" id="username" name="username" minlength="3" maxlength="20" required />
                </div>
                <div class="AspNet-Login-UserPanel">
                  <label class="Label" for="email">E-mail</label>
                  <input class="Text" id="email" type="email" name="email" required />
                </div>
              <?php else: ?>
                <div class="AspNet-Login-UserPanel">
                  <label class="Label" for="username">Character Name</label>
                  <input class="Text" id="username" name="username" required />
                </div>
              <?php endif; ?>
              <div class="AspNet-Login-PasswordPanel">
                <label class="Label" for="password">Password</label>
                <input class="Text" id="password" type="password" name="password" minlength="<?= $page === 'Register' ? '8' : '1' ?>" required />
              </div>
              <div class="AspNet-Login-SubmitPanel"><button class="Button" type="submit"><?= $page === 'Register' ? 'Register' : 'Login' ?></button></div>
            </form>
            <div class="AspNet-Login-SubmitPanel">
              <?php if ($page === 'Register'): ?>
                <a class="Button" href="<?= e(route('Login')) ?>">Login</a>
              <?php else: ?>
                <a class="Button" href="<?= e(route('Register')) ?>">Register</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php
$body = ob_get_clean();
