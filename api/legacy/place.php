<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/inc/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if(($_SERVER['REQUEST_METHOD']??'GET')!=='GET'){http_response_code(405);echo json_encode(['error'=>'method_not_allowed']);exit;}
$id=max(0,(int)($_GET['id']??0));
$q=$db->prepare("SELECT g.id,g.title,g.description,g.visits,g.created_at,u.username AS creator FROM games g LEFT JOIN users u ON u.id=g.creator_id WHERE g.id=? AND g.status='active'");
$q->execute([$id]);$game=$q->fetch();
if(!$game){http_response_code(404);echo json_encode(['error'=>'place_not_found']);exit;}
echo json_encode(['ok'=>true,'bridge_protocol'=>'rblx08-bridge-v1','place'=>$game],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
