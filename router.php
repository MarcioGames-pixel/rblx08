<?php
declare(strict_types=1);
$path=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/';
$aliases=[
 '/' => 'Default.php','/index.php'=>'Default.php','/Default.aspx'=>'Default.php',
 '/Games.aspx'=>'Games.php','/Catalog.aspx'=>'Catalog.php','/Browse.aspx'=>'Browse.php','/People.aspx'=>'People.php',
 '/Login.aspx'=>'Login.php','/Register.aspx'=>'Register.php','/Logout.aspx'=>'Logout.php',
 '/User.aspx'=>'User.php','/Item.aspx'=>'Item.php','/PlaceItem.aspx'=>'PlaceItem.php',
 '/Forum.aspx'=>'Forum/Default.php','/Forum/Default.aspx'=>'Forum/Default.php'
];
if(preg_match('~^/User/(\\d+)$~',$path,$m)){$_GET['id']=$m[1];$target='User.php';}
elseif(preg_match('~^/Forum/ShowPost\\.aspx$~',$path)){$target='Forum/ShowPost.php';}
elseif(preg_match('~^/Forum/CreatePost\\.aspx$~',$path)){$target='Forum/CreatePost.php';}
else{$target=$aliases[$path]??null;}
if($target===null){http_response_code(404);require __DIR__.'/inc/bootstrap.php';$title='Page not found';require __DIR__.'/inc/header.php';echo '<section class="Panel"><h1>Page not found</h1><a href="/">Return home</a></section>';require __DIR__.'/inc/footer.php';exit;}
require __DIR__.'/'.$target;
