<?php
declare(strict_types=1);
require __DIR__.'/inc/bootstrap.php';
$path=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/';
$routes=['/'=>'home','/index.php'=>'home','/Default.php'=>'home','/Default.aspx'=>'home','/Games.php'=>'games','/Games.aspx'=>'games','/Catalog.php'=>'catalog','/Catalog.aspx'=>'catalog','/Browse.php'=>'people','/Browse.aspx'=>'people','/People.php'=>'people','/People.aspx'=>'people','/Login.php'=>'login','/Login.aspx'=>'login','/Register.php'=>'register','/Register.aspx'=>'register','/Logout.php'=>'logout','/Logout.aspx'=>'logout','/User.php'=>'user','/User.aspx'=>'user','/Item.php'=>'item','/Item.aspx'=>'item','/PlaceItem.php'=>'games','/PlaceItem.aspx'=>'games','/Forum/Default.php'=>'forum','/Forum/Default.aspx'=>'forum','/Forum.php'=>'forum','/Forum.aspx'=>'forum'];
if(preg_match('~^/User/(\d+)$~',$path,$m)){$_GET['id']=$m[1];$path='/User.aspx';}
$page=$routes[$path]??'404';
if($page==='404')http_response_code(404);
require __DIR__.'/pages/index.php';
