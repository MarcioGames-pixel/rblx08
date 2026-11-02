<?php
require dirname(__DIR__).'/inc/page_helpers.php';
admin_required();
if(page_post()){
 check_csrf();$id=(int)($_POST['id']??0);$action=(string)($_POST['action']??'');
 if($action==='visibility'&&in_array($_POST['status']??'', ['active','hidden','removed'],true)){$db->prepare('UPDATE games SET status=? WHERE id=?')->execute([$_POST['status'],$id]);notice('Place status updated.');}
 if($action==='launch'){$url=trim((string)($_POST['launch_url']??''));if($url!==''&&(!filter_var($url,FILTER_VALIDATE_URL)||parse_url($url,PHP_URL_SCHEME)!=='https')){notice('Only valid HTTPS launch URLs are accepted.','error');go('/Admin/Games.php');}$db->prepare('UPDATE games SET launch_url=? WHERE id=?')->execute([$url?:null,$id]);notice('Launch URL saved.');}
 go('/Admin/Games.php');
}
$games=$db->query('SELECT g.id,g.title,g.status,g.launch_url,u.username FROM games g LEFT JOIN users u ON u.id=g.creator_id ORDER BY g.id DESC LIMIT 200')->fetchAll();
page_start('Place Administration');
?><section class="Panel"><h1>Place Administration</h1>
<table class="Grid"><tr><th>Place</th><th>Creator</th><th>Status</th><th>Browser launch URL</th><th>Actions</th></tr>
<?php foreach($games as $g):?><tr><td><?=e($g['title'])?></td><td><?=e($g['username']??'Unknown')?></td><td><?=e($g['status'])?></td><td><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="id" value="<?=(int)$g['id']?>"><input type="hidden" name="action" value="launch"><input type="url" name="launch_url" value="<?=e($g['launch_url'])?>" placeholder="https://..."><button>Save URL</button></form></td><td><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="id" value="<?=(int)$g['id']?>"><input type="hidden" name="action" value="visibility"><select name="status"><option value="active">Active</option><option value="hidden">Hidden</option><option value="removed">Removed</option></select><button>Update</button></form></td></tr><?php endforeach;?>
</table></section><?php page_end();?>