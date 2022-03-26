<?php
    include_once "Weather.php";

    //API ключ
    $apiKey = "576aaf3892b761136b4b5afacd0d8605";
    //Координаты
    $lat = 55.755811;
    $lon = 37.617617;

    $weather = new Weather($lat, $lon, $apiKey);
    $data = $weather->connect_weather();

    $light_day = $weather->find_light_day_distance($data, 5);
    $night_temp = $weather->find_min_night_temp_diff($data, 5);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Weather</title>
    </head>
    <body>
        <div id="light_day">
            <p>
                Самый длинный световой день: <?= $light_day["day"]; ?>
                -
                Продолжительность светового дня: <?= $light_day["day_long"]; ?> (часов)
            </p>
        </div>
        <div class="night_temp">
            <p>
                День с минимальной разницей между фактической и ощущаемой температурой: <?= $night_temp["day"]; ?>
                -
                Разница температур: <?= $night_temp["temp_diff"]; ?> (цельсий)
            </p>
        </div>
    </body>
</html>
