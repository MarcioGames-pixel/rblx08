<?php
$notice=take_notice();$logo=null;
foreach(['png','gif','jpg','jpeg','svg','webp'] as $ext){$f=__DIR__.'/../public/images/roblox-logo-2008.'.$ext;if(is_file($f)){$logo='/images/roblox-logo-2008.'.$ext;break;}}
$styles=__DIR__.'/../public/wayback/stylesheets.json';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e(($title??'Home').' - ROBLOX')?></title><link rel="stylesheet" href="/css/site.css">
<?php if(is_file($styles)):foreach((json_decode((string)file_get_contents($styles),true)?:[]) as $css):?><link rel="stylesheet" href="<?=e($css)?>"><?php endforeach;endif;?>
</head><body><div id="Container"><header id="Header"><a id="Logo" href="/"><?php if($logo):?><img src="<?=e($logo)?>" alt="ROBLOX"><?php else:?><span>ROBLOX</span><?php endif;?></a>
<div id="HeaderLinks"><?php if($currentUser):?>Welcome, <a href="/User.aspx?id=<?=(int)$currentUser['id']?>"><?=e($currentUser['username'])?></a> · <a href="/Logout.aspx">Logout</a><?php else:?><a href="/Login.aspx">Login</a> · <a href="/Register.aspx">Register</a><?php endif;?></div></header>
<nav id="Navigation"><a href="/">Home</a><a href="/Games.aspx">Games</a><a href="/Catalog.aspx">Catalog</a><a href="/Browse.aspx">People</a><a href="/Forum/Default.aspx">Forum</a></nav>
<?php if($notice):?><div class="notice <?=e($notice[1])?>"><?=e($notice[0])?></div><?php endif;?>
<main id="Body">
