<?php
    $vidu =
    "<?xml version='1.0' encoding='UTF-8'?>
    <mail>
        <hello>Xin chao</hello>
        <body>Lap trinh PHP!</body>
    </mail>";

    $vidu2 = simplexml_load_string($vidu) or die("Lỗi");
    print_r($vidu2);
?>