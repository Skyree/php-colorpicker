<?php declare(strict_types=1);

namespace Skyree\ColorPicker\Algorithm\LocalMaxima;

/**
 * Class Cell
 *
 * @author Loïc Boulakras
 * @author Arthur Guibert
 */
class Cell
{
    /** @var array */
    private $coordinates;

    /** @var int */
    private $count;

    /** @var array */
    private $rgbSum;

    /**
     * Cell constructor.
     * @param array $coordinates
     */
    public function __construct(array $coordinates)
    {
        $this->coordinates = $coordinates;
        $this->count = 0;
        $this->rgbSum = [0, 0, 0];
    }

    /**
     * @return array
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return array
     */
    public function getRgbSum()
    {
        return $this->rgbSum;
    }

    /**
     * @param \ImagickPixel $pixel
     * @throws
     */
    public function addPixel(\ImagickPixel $pixel)
    {
        $rgb = $pixel->getColor();

        $this->rgbSum[0] += $rgb['r'];
        $this->rgbSum[1] += $rgb['g'];
        $this->rgbSum[2] += $rgb['b'];

        $this->count += $pixel->getColorCount();
    }
}
