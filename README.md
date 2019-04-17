# php-colorpicker
_Pick a palette of dominant colors from an image_

### Requirements
* php 7.1
* ext-imagick

### Installation
Clone or download the repository and simply run `composer install`

### Demo
In order to use the demo, run the following command
```sh
cd demo
php -S localhost:8000
```
And open the url `localhost:8000` in your browser

### Getting started

Use the `Lol\ColorPicker\ColorPicker` class in your file

Example of usage:
```php
$imageUrl = 'http://some.website/some/image.ext';
$colorPicker = new ColorPicker();
$palette = $colorPicker->pick($imageUrl, ColorPicker::KMEANS, 7, 150);
foreach ($palette as $cluster) {
    echo sprintf("#%02x%02x%02x", ...$cluster['color']) . PHP_EOL; // will print up to 5 hex codes
}
```

### Options
ColorPicker::pick($imageUrl, $algorithm, $paletteSize, $resize);
* `$imageUrl` is the url of the image you want to pick colors from
* `$algorithm` is the algorithm to use among `ColorPicker::KMEANS`, `ColorPicker::WEIGHTED_KMEANS`, `ColorPicker::LOCAL_MAXIMA`
* `$paletteSize` is the maximum quantity of dominant colors you want to pick
* `$resize` is the width to which the image should be resized, it can be left empty
* `$quantization` is an array providing the colorspace and number of colors for the quantization, formatted as follow:
```php
$quantization = [
    'colorNumber' => 64,
    'colorSpace' => 1 // RGB space
];
```

### Algorithms
* `ColorPicker::KMEANS` uses a standard unsupervised Kmeans or Kmeans++ algorithm to calculate k clusters, some clusters can end up empty
* `ColorPicker::WEIGHTED_KMEANS` implementation of Kmeans from an histogram, calculating the clusters centroids according to coordinate and weight
* `ColorPicker::LOCAL_MAXIMA` keeps the maximum values among their 27 neighboring cells in a 3d space of quantized rgb values. Incompatible with quantization argument ! 
* `ColorPicker::QUANTIZATION` just quantize the colors and return the highest clusters
