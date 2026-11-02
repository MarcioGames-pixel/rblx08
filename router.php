<?php
declare(strict_types=1);
require __DIR__.'/inc/bootstrap.php';
$path=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/';
$routes=['/'=>'home','/Default.aspx'=>'home','/Games.aspx'=>'games','/Catalog.aspx'=>'catalog','/Browse.aspx'=>'people','/People.aspx'=>'people','/Login.aspx'=>'login','/Register.aspx'=>'register','/Logout.aspx'=>'logout','/User.aspx'=>'user','/Item.aspx'=>'item','/Forum/Default.aspx'=>'forum','/Forum.aspx'=>'forum'];
if(preg_match('~^/User/(\d+)$~',$path,$m)){$_GET['id']=$m[1];$path='/User.aspx';}
$page=$routes[$path]??'404';
if($page==='404')http_response_code(404);
require __DIR__.'/pages/index.php';
