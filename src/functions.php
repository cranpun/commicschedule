<?php
date_default_timezone_set('Asia/Tokyo');

require_once(join(DIRECTORY_SEPARATOR, [__DIR__, "..", "vendor", "autoload.php"]));
$dotenv = \Dotenv\Dotenv::createImmutable(join(DIRECTORY_SEPARATOR, [__DIR__, ".."]));
$dotenv->load();

/**
 * https://webservice.rakuten.co.jp/api/booksbooksearch/
 */
function downloadData($page, $size, $publisherName, $seriesName)
{
    $client = new \GuzzleHttp\Client();
    $query = [
        "publisherName" => $publisherName,
        "applicationId" => $_ENV["APPLICATIONID"],
        "size" => $size,
        "sort" => "-releaseDate",
        "page" => $page,
    ];
    if($seriesName) {
        $query["seriesName"] = $seriesName;
    }
    $response = $client->request("GET", "https://app.rakuten.co.jp/services/api/BooksBook/Search/20170404", [
        "query" => $query
    ]);
    $ret = json_decode($response->getBody()->getContents());
    return $ret;
}

function parseDate($date)
{
    $str = mb_ereg_replace("年", "-", mb_ereg_replace("月", "-", mb_ereg_replace("日", "", mb_ereg_replace("頃", "", $date))));
    try {
        return new \Carbon\Carbon($str);
    } catch (Exception $e) {
        // パースエラー　→　発売日が確定していない（頃、中旬等）
        return false;
    }
}

function makeDataPages($size, $publisherName, $seriesName, $comics, $range)
{
    $ret = [];
    $page = 1; // 1からだと未来すぎるため。適当に差っ引く
    do {
        echo "{$publisherName}.{$page}" . PHP_EOL;
        $raws = downloadData($page, $size, $publisherName, $seriesName);

        $clms = ["title", "author", "publisherName", ];
        $inRange = true;
        foreach($raws->Items as $row) {
            $item = $row->Item;
            // 年月日の日本語なのでCarbonに変換
            $itdate = parseDate($item->salesDate);
            // echo "{$itdate->format('Y-m-d')}" . PHP_EOL;
            if($itdate == false || $itdate->gte($range["end"])) {
                // 日付が今日より未来なら飛ばす
                continue;
            } else if($itdate->lt($range["start"])) {
                echo "{$itdate->format('Y-m-d') } < {$range['start']->format('Y-m-d')}" . PHP_EOL;
                // 日付が開始日より前なら終了
                $inRange = false;
                break;
            }

            // 範囲内のデータのため、データ追加
            $r = [];
            foreach($clms as $clm) {
                $r[$clm] = $item->{$clm};
            }
            // 日付は形式を指定
            $r["salesDate"] = $itdate->format("Y-m-d");
            $r["mine"] = "other";
            // 購入コミックスであれば
            foreach($comics as $comic) {
                if(mb_strstr($r["title"], $comic)) {
                    // 購入コミックス
                    $r["mine"] = "mine";
                    break;
                }
            }
            $ret[] = $r;
        }
        $page++;
        // 連続アクセスはリジェクトされるので一定時間待つ
        sleep(1); // 秒
    } while ($inRange && $page <= 100);
    return $ret;
}

function makeData($publisherNames, $comics)
{
    // 日付範囲。概ね前2ヶ月、あと1ヶ月
    // // 先々月の1日
    $range["start"] = new \Carbon\Carbon();
    $range["start"]->subMonth(2);
    $range["start"]->setDay(1);
    // // 再来月の1日
    $range["end"] = new \Carbon\Carbon();
    $range["end"]->addMonth(2);
    $range["end"]->setDay(1);

    // 出版社ごとにデータをロード
    $rows = [];
    foreach($publisherNames as $publisherName) 
    {
        // コミックス（size=9）。シリーズは全部
        $rows = array_merge($rows, makeDataPages(9, $publisherName, null, $comics, $range));
    }
    // 電撃文庫のみ特別対応。角川の電撃文庫。sizeは文庫（2）
    $rows = array_merge($rows, makeDataPages(2, "KADOKAWA", "電撃文庫", $comics, $range));

    // echo json_encode($ret);

    // 日付順にソート
    array_multisort(array_column($rows, 'salesDate'), SORT_DESC, $rows);

    $now = \Carbon\Carbon::now();

    $ret = [
        "created_at" => $now->format("Y-m-d H:i:s"),
        "range" => [
            "start" => $range["start"]->format("Y-m-d"),
            "end" => $range["end"]->format("Y-m-d"),
        ],
        "rows" => $rows,
    ];
    return $ret;
}

function loadOutput()
{
    $json = file_get_contents(join(DIRECTORY_SEPARATOR, [__DIR__, "..", "output", "output.json"]));
    $ret = json_decode($json);
    return $ret;
}


// $raws = loadComic(1);
// // print_r($raws);
// $rows = parseComic($raws);
// print_r($rows);