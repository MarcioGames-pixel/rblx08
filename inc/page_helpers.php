<?php
declare(strict_types=1);
require_once __DIR__.'/bootstrap.php';
function page_start(string $title): void { $GLOBALS['title']=$title; $currentUser=$GLOBALS['currentUser']??null; require __DIR__.'/header.php'; }
function page_end(): void { require __DIR__.'/footer.php'; }
function page_error(string $message): void { echo '<p class="notice error">'.e($message).'</p>'; }
function page_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }
