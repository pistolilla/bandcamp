<?php
require '../functions.php';
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// init
$db = new MyDB();

// GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT tag, COUNT(*) AS c
            FROM url_tag
            GROUP BY tag
            HAVING c > 2
            ORDER BY c DESC";
}
// POST
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $tags = $_POST['tags'];
        $tagsArray = json_decode($tags, true);
        $tagsArray = array_map($escapeQuotes, $tagsArray);
        $tagsStr = "'" . join("', '", $tagsArray) . "'";
    } catch (Exception $e) {
        $tagsStr = "''";
    }

    // query
    $sql = "SELECT Tag, COUNT(*) AS c
            FROM url_tag
            WHERE Url IN (
                SELECT Url FROM url_tag
                WHERE Tag IN ($tagsStr)
            )
            AND Tag NOT IN ($tagsStr)
            GROUP BY Tag
            ORDER BY c DESC";
}
$res = $db->query($sql);

$result = array();
while($row = $res->fetchArray(SQLITE3_ASSOC)) {

  // reading columns
  $obj = new Tag();
  $obj->Tag = (string)$row['Tag'];
  $obj->c = (string)$row['c'];

  array_push($result, $obj);
}
$db->close();

$myJSON = json_encode($result);
echo $myJSON;

?>