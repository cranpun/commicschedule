<?php
date_default_timezone_set('Asia/Tokyo');

require_once(join(DIRECTORY_SEPARATOR, [__DIR__, "..", "src", "functions.php"]));
require_once(join(DIRECTORY_SEPARATOR, [__DIR__, "..", "conf", "publisherName.php"]));
require_once(join(DIRECTORY_SEPARATOR, [__DIR__, "..", "conf", "comics.php"]));

$rows = makeData($publisherNames, $comics);
$json = json_encode($rows);

$path = join(DIRECTORY_SEPARATOR, [__DIR__, "..", "output", "output.json"]);
file_put_contents($path, $json);
