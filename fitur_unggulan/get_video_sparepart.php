<?php
require_once __DIR__ . '/../fungsi/dp.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$result = ['success' => false, 'spareparts' => []];

if ($id > 0) {
    $query = "SELECT id, name, specification as spec, price FROM video_spareparts WHERE video_id = $id";
    $res = mysqli_query($conn, $query);
    if ($res) {
        $spareparts = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $spareparts[] = $row;
        }
        $result = ['success' => true, 'spareparts' => $spareparts];
    } else {
        $result['message'] = mysqli_error($conn);
    }
}
header('Content-Type: application/json');
echo json_encode($result);
?>