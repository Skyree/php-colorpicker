<?php declare(strict_types=1);

namespace Skyree\ColorPicker\Algorithm\WeightedKmeans;

/**
 * Class Cluster
 *
 * @author Loïc Boulakras
 */
class Cluster
{
    /** @var \ImagickPixel[]  */
    private $points;

    /** @var \ImagickPixel  */
    private $center;

    /** @var int */
    private $dimension;

    /**
     * Cluster constructor.
     *
     * @param array $points
     * @param \ImagickPixel $center
     * @param int $dimension
     */
    public function __construct(array $points, \ImagickPixel $center, int $dimension)
    {
        $this->points = $points;
        $this->center = $center;
        $this->dimension = $dimension;
    }

    /**
     * @return \ImagickPixel
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * @return array|\ImagickPixel[]
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @return int
     */
    public function getDimension()
    {
        return $this->dimension;
    }
}
