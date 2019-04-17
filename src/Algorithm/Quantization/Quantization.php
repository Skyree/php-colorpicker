<?php declare(strict_types=1);

namespace Skyree\ColorPicker\Algorithm\Quantization;

/**
 * Class Quantization
 *
 * @author Loïc Boulakras
 */
class Quantization
{
    /**
     * @param \Imagick $image
     * @param int $k
     * @return array
     * @throws
     */
    public function clusterize(\Imagick $image, int $k = 1): array
    {
        $points = $image->getImageHistogram();

        usort($points, function (\ImagickPixel $pointA, \ImagickPixel $pointB) {
            return $pointB->getColorCount() - $pointA->getColorCount();
        });

        return array_slice(array_map(function (\ImagickPixel $point) {
            return [
                'color' => array_values($point->getColor()),
                'count' => $point->getColorCount()
            ];
        }, $points), 0, $k);
    }
}
