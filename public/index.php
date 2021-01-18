<?php
require_once(join(DIRECTORY_SEPARATOR, [__DIR__, "..", "src", "functions.php"]));
$comicschedule = loadOutput();
?><!DOCTYPE html>
<html lang="ja" style="height: 100%;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="minimum-scale=1, initial-scale=1, width=device-width, shrink-to-fit=no">
    <meta name="description" content="comicschedule">
    <link rel="shortcut icon" href="https://tm.cranpun-tool.ml/wp-content/themes/themeorg/favicon.ico" />

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
    </style>
</head>

<body id="body" style="display: flex; flex-flow: column; min-height: 100vh;">
    <header id="header">
        <nav class="navbar is-info" role="navigation">
            <section class="navbar-brand">
                <a class="navbar-item" href="/">
                    <img src="https://hp.cranpun-tool.ml/wp-content/uploads/cranpun-lab_mark_trans.png" alt="cranpun-lab">
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
                    <a class="navbar-item">menuL1</a>
                    <a class="navbar-item">menuL2</a>
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
                        <tr>
                            <td style="white-space: nowrap"><?= $row->salesDate ?></td>
                            <td><?= $row->title ?></td>
                            <td><?= $row->author ?></td>
                            <td style="white-space: nowrap"><?= $row->publisherName ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endforeach; ?>
            </div>
        <section>
    </main>
    <footer id="footer" class="footer">
        <section class="content has-text-centered">
        &copy; cranpun-lab
        </section>
    </footer>
</body>

</html>