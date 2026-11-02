<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/inc/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function api_reply(int $status,array $data):never{http_response_code($status);echo json_encode($data,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);exit;}
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST')api_reply(405,['error'=>'method_not_allowed']);
if(!$currentUser)api_reply(401,['error'=>'authentication_required']);
$csrfHeader=(string)($_SERVER['HTTP_X_CSRF_TOKEN']??'');
if($csrfHeader===''||!hash_equals(csrf(),$csrfHeader))api_reply(419,['error'=>'csrf_failed']);
$input=json_decode((string)file_get_contents('php://input'),true);
$placeId=is_array($input)?(int)($input['place_id']??0):0;
if($placeId<1)api_reply(422,['error'=>'invalid_place_id']);
$q=$db->prepare("SELECT id,title FROM games WHERE id=? AND status='active'");
$q->execute([$placeId]);$place=$q->fetch();
if(!$place)api_reply(404,['error'=>'place_not_found']);
$token=bin2hex(random_bytes(32));
$expires=time()+300;
$insert=$db->prepare('INSERT INTO legacy_join_tickets(user_id,game_id,token_hash,expires_at) VALUES(?,?,?,FROM_UNIXTIME(?))');
$insert->execute([(int)$currentUser['id'],$placeId,hash('sha256',$token),$expires]);
api_reply(201,['ok'=>true,'bridge_protocol'=>'rblx08-bridge-v1','join_token'=>$token,'expires_in'=>300,'place'=>['id'=>(int)$place['id'],'title'=>$place['title']],'redeem_endpoint'=>'/api/legacy/redeem.php']);
