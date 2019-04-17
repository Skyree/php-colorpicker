<?php declare(strict_types=1);

namespace Skyree\ColorPicker\Algorithm\Kmeans;

use KMeans\Cluster;
use KMeans\Space;

/**
 * Class Kmeans
 *
 * @author Loïc Boulakras
 */
class Kmeans
{
    /**
     * @param \Imagick $image
     * @param int $k
     * @return array
     * @throws \ImagickPixelException
     */
    public function clusterize(\Imagick $image, int $k)
    {
        $space = new Space(3);
        $iterator = $image->getPixelIterator();

        foreach ($iterator as $row) {
            foreach ($row as $pixel) {
                /** @var \ImagickPixel $pixel*/
                $coordinates = array_slice(array_values($pixel->getColor()), 0, 3);
                $space->addPoint($coordinates);
            }
        }

        /** @var Cluster[] $clusters */
        $clusters = $space->solve($k, Space::SEED_DASV);
        usort($clusters, function (Cluster $clusterA, Cluster $clusterB) {
            return $clusterB->count() - $clusterA->count();
        });

        return $this->format($clusters);
    }

    /**
     * @param array $clusters
     * @return array
     */
    private function format(array $clusters): array
    {
        return array_map(function(Cluster $cluster) {
            return [
                'color' => $cluster->getCoordinates(),
                'count' => $cluster->count(),
            ];
        }, $clusters);
    }
}
