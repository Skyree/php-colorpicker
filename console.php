<?php declare(strict_types=1);

require './vendor/autoload.php';

use Skyree\ColorPicker\ColorPicker;

$imageUrl = 'https://ksassets.timeincuk.net/wp/uploads/sites/55/2018/10/muse-920x584.jpg';
// $imageUrl = 'https://e-cdns-images.dzcdn.net/images/cover/14372848c639d82a681aceaa8c8618ea/264x264-000000-80-0-0.jpg';

$colorPicker = new ColorPicker();
$clusters = $colorPicker->pick($imageUrl, ColorPicker::WEIGHTED_KMEANS, 7, 150);

foreach ($clusters as $cluster) {
    $color = $cluster['color'];
    echo sprintf("#%02x%02x%02x\t- %d", (int) $color[0], (int) $color[1], (int) $color[2], $cluster['count']) . PHP_EOL;
}
