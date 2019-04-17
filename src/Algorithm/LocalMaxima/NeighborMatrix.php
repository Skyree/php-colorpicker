<?php declare(strict_types=1);

namespace Skyree\ColorPicker\Algorithm\LocalMaxima;

/**
 * Class NeighborMatrix
 *
 * @author Loïc Boulakras
 * @author Arthur Guibert
 */
class NeighborMatrix
{
    /**
     * @param int|null $depth
     * @return array
     */
    public static function build(?int $depth = 1): array
    {
        $matrix = [];
        for ($x = -$depth; $x <= $depth; $x++) {
            for ($y = -$depth; $y <= $depth; $y++) {
                for ($z = -$depth; $z <= $depth; $z++) {
                    $matrix[] = [$x, $y, $z];
                }
            }
        }
        return $matrix;
    }
}
