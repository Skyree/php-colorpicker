<?php declare(strict_types=1);

namespace Skyree\ColorPicker\Algorithm\LocalMaxima;

/**
 * Class LocalMaxima
 *
 * @author Loïc Boulakras
 * @author Arthur Guibert
 */
class LocalMaxima
{
    /**
     * @param \Imagick $image
     * @param int $paletteSize
     * @return array
     */
    public function clusterize(\Imagick $image, int $paletteSize): array
    {
        $histogram = $image->getImageHistogram();

        $clusters = $this->getClusters($histogram);
        return array_slice($clusters, 0, min($paletteSize, count($clusters)));
    }

    /**
     * @param array $histogram
     * @return array
     */
    private function getClusters(array $histogram)
    {
        $cells = $this->quantizeHistogram($histogram);

        $neighborMatrix = NeighborMatrix::build();
        $cells =  array_filter($cells, function (Cell $cell) use ($cells, $neighborMatrix) {
            return $this->isLocalMaximum($cell, $cells, $neighborMatrix);
        });

        $clusters = array_map(function(Cell $cell) {
            $average = [
                $cell->getRgbSum()[0] / $cell->getCount(),
                $cell->getRgbSum()[1] / $cell->getCount(),
                $cell->getRgbSum()[2] / $cell->getCount(),
            ];
            $brightness = max(max($average[0], $average[1]), $average[2]);
            return [
                'color' => $average,
                'brightness' => $brightness,
                'count' => $cell->getCount(),
            ];
        }, $cells);

        uasort($clusters, function(array $a, array $b) {
            return $b['count'] - $a['count'];
        });

        return $clusters;
    }

    /**
     * @param Cell $cell
     * @param array $cells
     * @param array $neighbourRelativeIndices
     * @return bool
     */
    private function isLocalMaximum(Cell $cell, array $cells, array $neighbourRelativeIndices): bool
    {
        $maximum = true;
        foreach ($neighbourRelativeIndices as $neighbourRelativeIndex) {
            $coordinates = $cell->getCoordinates();
            $neighbourCluster = [
                $coordinates[0] + $neighbourRelativeIndex[0],
                $coordinates[1] + $neighbourRelativeIndex[1],
                $coordinates[2] + $neighbourRelativeIndex[2],
            ];

            $neighbourIndex = implode('-', $neighbourCluster);
            if (isset($cells[$neighbourIndex])) {
                if ($cells[$neighbourIndex]->getCount() > $cell->getCount()) {
                    $maximum = false;
                    break;
                }
            }
        }
        return $maximum;
    }

    /**
     * @param \ImagickPixel[] $histogram
     * @return Cell[]
     * @throws
     */
    private function quantizeHistogram(array $histogram)
    {
        $cells = [];
        foreach ($histogram as $pixel) {
            $cluster = [
                ($pixel->getColor()['r'] * 30) >> 8,
                ($pixel->getColor()['g'] * 30) >> 8,
                ($pixel->getColor()['b'] * 30) >> 8,
            ];

            $index = implode('-', $cluster);
            $cell = $cells[$index] ?? new Cell($cluster);
            $cell->addPixel($pixel);

            $cells[$index] = $cell;
        }
        return $cells;
    }
}
