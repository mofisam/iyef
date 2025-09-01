<?php
require_once '../../config/db.php';
require_once '../../includes/functions/speakers.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Speaker ID not provided']);
    exit;
}

$speakerId = (int)$_GET['id'];
$speaker = getSpeakerById($speakerId);

if ($speaker) {
    echo json_encode($speaker);
} else {
    echo json_encode(['error' => 'Speaker not found']);
}