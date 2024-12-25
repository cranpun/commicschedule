<?php
require_once(join(DIRECTORY_SEPARATOR, [__DIR__, "..", "src", "functions.php"]));
$comicschedule = loadOutput();
?><!DOCTYPE html>
<html lang="ja" style="height: 100%;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="minimum-scale=1, initial-scale=1, width=device-width, shrink-to-fit=no">
    <meta name="description" content="comicschedule">
    <link rel="shortcut icon" href="https://cranpun.sub.jp/tm/wp-content/themes/themeorg/favicon.ico" />

    <!-- Bulma -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.1/css/bulma.min.css" />

    <!-- fontawesome5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />

    <!-- my library -->
    <!-- <link rel="stylesheet" href="style.css" /> -->
    <!-- <script type="text/javascript" src="dist/bundle.js"></script> -->

    <title>comicschedule</title>
    <style type="text/css">
        @keyframes fadein {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
        @keyframes fadeout {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
        .fadein {
            animation-name: fadein;
            animation-duration: 500ms;
            animation-timing-function: ease;
        }
        .is-checked {
            background-color: pink!important;
        }
    </style>
</head>

<body id="body" style="display: flex; flex-flow: column; min-height: 100vh;">
    <header id="header">
        <nav class="navbar is-info" role="navigation">
            <section class="navbar-brand">
                <a class="navbar-item" href="/">
                    <img src="https://cranpun.sub.jp/cranpun-lab/wp-content/uploads/cranpun-lab_mark_trans.png" alt="cranpun-lab">
                </a>
                <a id="burger" role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbar-headermenu">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
                <script type="text/javascript">
                    window.addEventListener("load", function() {
                        const burger = document.querySelector("#burger");
                        const target = document.getElementById(burger.dataset.target);
                        burger.addEventListener("click", function() {
                            burger.classList.toggle("is-active");
                            burger.classList.toggle("fadein");
                            target.classList.toggle("is-active");
                            target.classList.toggle("fadein");
                        })
                    })
                </script>
            </section>
            <section id="navbar-headermenu" class="navbar-menu">
                <div class="navbar-start">
                    <a id="act-list-open" class="navbar-item">list</a>
                </div>
                <div class="navbar-end">
                    <a class="navbar-item">menuR1</a>
                    <a class="navbar-item">menuR2</a>
                </div>
            </section>
        </nav>
    </header>
    <main id="main" style="flex: 1;">
        <section class="hero is-primary">
            <div class="hero-body">
                <section class="container">
                    <h1 class="title">comicschedule</h1>
                    <h2 class="subtitle"></h2>
                </section>
            </div>
        </section>
        <section id="contents" class="section">
            <div class="container">
                <div>
                    <ul>
                        <li>created_at : <?= $comicschedule->created_at ?></li>
                        <li>range : <?= $comicschedule->range->start ?>～<?= $comicschedule->range->end ?></li>
                    </ul>
                </div>
                <hr/>
                <?php foreach(["mine", "other"] as $mine) : ?>
                <div>
                    <span class="tag is-info"><?= array_count_values(array_column($comicschedule->rows, "mine"))[$mine] ?></span>
                </div>
                <table class="table is-bordered is-striped is-narrow is-fullwidth">
                    <thead>
                        <tr>
                            <th>発売日</th>
                            <th>タイトル</th>
                            <th>著者</th>
                            <th>出版社</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($comicschedule->rows as $row) : if($row->mine != $mine) { continue; } ?>
                        <tr class="datatr">
                            <td class="td-salesDate" style="white-space: nowrap"><?= $row->salesDate ?></td>
                            <td class="td-title"><?= $row->title ?></td>
                            <td class="td-author"><?= $row->author ?></td>
                            <td class="td-publisherName" style="white-space: nowrap"><?= $row->publisherName ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endforeach; ?>
            </div>
        <section>

        <section>
        <div id="modal-list" class="modal">
            <div class="modal-background"></div>
            <div class="modal-content">
                <textarea id="area-list" style="width: 100%; padding: 8px;" rows="15" readonly></textarea>
            </div>
            <button id="act-list-close" class="modal-close is-large" aria-label="close"></button>
        </div>
        </section>
    </main>
    <footer id="footer" class="footer">
        <section class="content has-text-centered">
        &copy; cranpun-lab
        </section>
    </footer>
    <script type="text/javascript">
    window.addEventListener("load", () => {
        document.querySelectorAll(".datatr").forEach((tr) => {
            tr.addEventListener("click", (e) => {
                const nowtd = e.target;
                const nowtr = nowtd.parentElement;
                nowtr.classList.toggle("is-checked");
            });
        });
        document.querySelector("#act-list-open").addEventListener("click", () => {
            const list = [];
            document.querySelectorAll(".is-checked").forEach((tr) => {
                const salesDate = tr.querySelector(".td-salesDate").innerText;
                const title = tr.querySelector(".td-title").innerText;
                const author = tr.querySelector(".td-author").innerText;
                const publisherName = tr.querySelector(".td-publisherName").innerText;
                const row = `${salesDate} ${title} ${author} ${publisherName}`;
                list.push(row);
            });

            if(list.length > 0) {
                document.querySelector("#area-list").value = "・" + list.join("\n・");
            } else {
                document.querySelector("#area-list").value = "（選択なし）";
            }

            const modal = document.querySelector("#modal-list");
            modal.classList.add("is-active");
        });
        const closefunc = () => {
            const modal = document.querySelector("#modal-list");
            modal.classList.remove("is-active");
        };
        document.querySelector("#act-list-close").addEventListener("click", closefunc);
        document.querySelector(".modal-background").addEventListener("click", closefunc);
    });
    </script>
</body>

</html>
