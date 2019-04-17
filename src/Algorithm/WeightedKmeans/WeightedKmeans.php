<?php declare(strict_types=1);

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skyree\ColorPicker\Algorithm\WeightedKmeans;

/**
 * Class WeightedKmeans
 *
 * @author Loïc Boulakras
 */
class WeightedKmeans
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
        $clusters = $this->kmeans($points, $k, 1);

        usort($clusters, function (Cluster $clusterA, Cluster $clusterB) {
            return count($clusterB->getPoints()) - count($clusterA->getPoints());
        });

        return array_map(function (Cluster $cluster) {
            return [
                'color' => array_values($cluster->getCenter()->getColor()),
                'count' => count($cluster->getPoints())
            ];
        }, $clusters);
    }

    /**
     * @param \ImagickPixel $point1
     * @param \ImagickPixel $point2
     * @return float
     * @throws
     */
    private function euclidean(\ImagickPixel $point1, \ImagickPixel $point2): float
    {
        $values = [];
        foreach (range(0, 2) as $i) {
            $values[] = pow(array_values($point1->getColor())[$i] - array_values($point2->getColor())[$i], 2);
        }

        return sqrt(array_sum($values));
    }

    /**
     * @param \ImagickPixel[] $points
     * @param int $dimension
     * @return \ImagickPixel
     * @throws
     */
    private function calculateCenter(array $points, int $dimension): \ImagickPixel
    {
        $pointLength = 0;
        $vals = [];
        foreach (range(0, $dimension - 1) as $i) {
            $vals[] = (float) 0.0;
        }

        foreach ($points as $point) {
            $pointLength += $point->getColorCount();
            foreach (range(0, $dimension - 1) as $i) {
                $vals[$i] += (float) (array_values($point->getColor())[$i] * $point->getColorCount());
            }
        }

        if ($pointLength <= 0) {
            $pointLength = 1;
        }

        $coord = [];
        foreach ($vals as $val) {
            $coord[] = $val / $pointLength;
        }

        $point = new \ImagickPixel(sprintf('rgb(%d,%d,%d)', ...$coord));
        $point->setcolorCount(1);
        return $point;
    }

    /**
     * @param \ImagickPixel[] $points
     * @param int $k
     * @param int $minDiff
     * @return Cluster[]
     */
    private function kmeans(array $points, int $k, int $minDiff): array
    {
        /** @var Cluster[] $clusters */
        $clusters = [];
        foreach (array_rand($points, $k) as $samplePointKey) {
            $samplePoint = $points[$samplePointKey];
            $clusters[] = new Cluster([$samplePoint], $samplePoint, 3);
        }

        $index = 0;
        while (true) {
            $plists = [];
            foreach (range(0, $k - 1) as $i) {
                $plists[] = [];
            }

            foreach ($points as $point) {
                $smallestDistance = (float) INF;
                foreach (range(0, $k - 1) as $i) {
                    $distance = $this->euclidean($point, $clusters[$i]->getCenter());
                    if ($distance < $smallestDistance) {
                        $smallestDistance = $distance;
                        $index = $i;
                    }
                }
                $plists[$index][] = $point;
            }
            $diff = 0;
            foreach (range(0, $k - 1) as $i) {
                $old = $clusters[$i];
                $center = $this->calculateCenter($plists[$i], $old->getDimension());
                $new = new Cluster($plists[$i], $center, $old->getDimension());
                $clusters[$i] = $new;
                $diff = max($diff, $this->euclidean($old->getCenter(), $new->getCenter()));
            }

            if ($diff < $minDiff) {
                break;
            }
        }

        return $clusters;
    }
}
