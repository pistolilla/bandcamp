<?php
require '../functions.php';
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// init
$db = new MyDB();

// reading params
try {
    $tags = $_POST['tags'];
    $tagsObj = json_decode($tags, true);
    // escaping single quotes
    $tagsArr1 = array_map($escapeQuotes, $tagsObj["tags1"]);
    $tagsArr2 = array_map($escapeQuotes, $tagsObj["tags2"]);
    // embedding
    $tagsStr1 = "'" . join("', '", $tagsArr1) . "'";
    $tagsStr2 = "'" . join("', '", $tagsArr2) . "'";
} catch (Exception $e) {
    $tagsStr = "''";
}

// query
$sql = "SELECT Url, Artist, Album, c, GROUP_CONCAT(Tag, ', ') AS Tags
        FROM (
            SELECT url_info.rowid, Url, Artist, Album, COUNT(Album) AS c, Tag
            FROM url_info
                LEFT JOIN url_tag USING(Url)
            WHERE Url IN (
                SELECT Url FROM url_tag
                WHERE Tag IN ($tagsStr1)
            )
            AND Url IN (
                SELECT Url FROM url_tag
                WHERE Tag IN ($tagsStr2)
            )
            GROUP BY Artist, Tag
        )
        GROUP BY Artist
        ORDER BY c DESC, rowid DESC";

$res = $db->query($sql);

$result = array();
while($row = $res->fetchArray(SQLITE3_ASSOC)) {

  // reading columns
  $obj = new Artist();
  $obj->Url = (string)$row['Url'];
  $obj->Artist = (string)$row['Artist'];
  $obj->Album = (string)$row['Album'];
  $obj->Tags = (string)$row['Tags'];
  $obj->c = (string)$row['c'];

  array_push($result, $obj);
}
$db->close();

$myJSON = json_encode($result);
echo $myJSON;

?>