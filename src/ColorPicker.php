<?php declare(strict_types=1);

namespace Skyree\ColorPicker;

use Skyree\ColorPicker\Algorithm\Kmeans\Kmeans;
use Skyree\ColorPicker\Algorithm\LocalMaxima\LocalMaxima;
use Skyree\ColorPicker\Algorithm\Quantization\Quantization;
use Skyree\ColorPicker\Algorithm\WeightedKmeans\WeightedKmeans;

/**
 * Class ColorPicker
 *
 * @author Loïc Boulakras
 */
class ColorPicker
{
    const KMEANS = 'kmeans';
    const WEIGHTED_KMEANS = 'weightedKmeans';
    const LOCAL_MAXIMA = 'localMaxima';
    const QUANTIZATION = 'quantization';

    /**
     * @param string $imageUrl
     * @param string $algorithm
     * @param int $paletteSize
     * @param int|null $resize
     * @param array $quantization
     * @return array
     */
    public function pick(string $imageUrl, $algorithm = self::KMEANS, int $paletteSize = 5, ?int $resize = null, array $quantization = []): array
    {
        try {
            $image = new \Imagick();
            $image->readImage($imageUrl);

            if ($resize !== null) {
                $image->scaleImage($resize, 0);
            }

            if (isset($quantization['colorNumber']) && isset($quantization['colorSpace'])) {
                $image->quantizeImage($quantization['colorNumber'], $quantization['colorSpace'], 8, false, false);
            }

            if ($algorithm === self::KMEANS) {
                $algorithm = new Kmeans();
            } elseif ($algorithm === self::WEIGHTED_KMEANS) {
                $algorithm = new WeightedKmeans();
            } elseif ($algorithm === self::QUANTIZATION) {
                $algorithm = new Quantization();
            } else {
                $algorithm = new LocalMaxima();
            }

            return $algorithm->clusterize($image, $paletteSize);

        } catch (\ImagickException | \ImagickPixelException $exception) {
            return [];
        }
    }
}
