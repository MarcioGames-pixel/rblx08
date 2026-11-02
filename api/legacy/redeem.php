<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/inc/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function api_reply(int $status,array $data):never{http_response_code($status);echo json_encode($data,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);exit;}
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST')api_reply(405,['error'=>'method_not_allowed']);
$input=json_decode((string)file_get_contents('php://input'),true);
$token=is_array($input)?(string)($input['join_token']??''):'';
if(!preg_match('/^[a-f0-9]{64}$/',$token))api_reply(422,['error'=>'invalid_join_token']);
try{
 $db->beginTransaction();
 $q=$db->prepare('SELECT t.id,t.user_id,t.game_id,u.username,g.title,g.description FROM legacy_join_tickets t JOIN users u ON u.id=t.user_id JOIN games g ON g.id=t.game_id WHERE t.token_hash=? AND t.used_at IS NULL AND t.expires_at>NOW() AND u.account_status=\'active\' AND g.status=\'active\' FOR UPDATE');
 $q->execute([hash('sha256',$token)]);$ticket=$q->fetch();
 if(!$ticket){$db->rollBack();api_reply(401,['error'=>'join_token_invalid_or_expired']);}
 $db->prepare('UPDATE legacy_join_tickets SET used_at=NOW() WHERE id=?')->execute([$ticket['id']]);
 $db->commit();
 api_reply(200,['ok'=>true,'bridge_protocol'=>'rblx08-bridge-v1','player'=>['id'=>(int)$ticket['user_id'],'username'=>$ticket['username']],'place'=>['id'=>(int)$ticket['game_id'],'title'=>$ticket['title'],'description'=>$ticket['description']],'message'=>'Identity validated. A compatible game server is still required to start gameplay.']);
}catch(Throwable $e){if($db->inTransaction())$db->rollBack();api_reply(500,['error'=>'session_validation_failed']);}
