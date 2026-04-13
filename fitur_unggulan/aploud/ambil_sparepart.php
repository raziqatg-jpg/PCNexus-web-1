<?php
session_start();
require_once __DIR__ . '/../../fungsi/dp.php';

$video_id = intval($_GET['id'] ?? 0);

$query = "SELECT * FROM video_spareparts WHERE video_id = $video_id";
$result = mysqli_query($conn, $query);

$spareparts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $spareparts[] = $row;
}

echo json_encode(['success' => true, 'spareparts' => $spareparts]);
?>