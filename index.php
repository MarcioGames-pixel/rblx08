<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';

$page=trim((string)($_GET['page']??'Default'),'/');
$controllers=[
 'Default'=>'pages/home.php',
 'Games'=>'pages/games.php',
 'Catalog'=>'pages/catalog.php',
 'Browse'=>'pages/browse.php',
 'Item'=>'pages/item.php',
 'User'=>'pages/user.php',
 'Inventory'=>'pages/inventory.php',
 'Login'=>'pages/auth.php',
 'Register'=>'pages/auth.php',
 'Forum/ShowForum'=>'pages/forum-list.php',
 'Forum/ShowPost'=>'pages/forum-post.php',
];

if(!isset($controllers[$page])){
 http_response_code(404);
 $title='Page not found';
 $body='<section class="panel"><h1>Page not found</h1><p>The requested page does not exist.</p></section>';
}else{
 $title='Home';
 $body='';
 $user=logged();
 require __DIR__.'/'.$controllers[$page];
}

require __DIR__.'/app/layout.php';
