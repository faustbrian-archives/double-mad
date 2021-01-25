<?php

declare(strict_types=1);

/**
 * Copyright (c) Konceiver Oy <legal@konceiver.dev>.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Konceiver\DoubleMadOutlier;

final class DoubleMadOutlier
{
    protected array $data;

    private float |

 int $cutoff;

    private float |

 int $median;

    public function __construct(array $data, float | int $cutoff = 4)
    {
        $this->data   = $data;
        $this->cutoff = $cutoff;
        $this->median = collect($this->data)->median();

        asort($this->data, SORT_NUMERIC);
    }

    public function doubleMad(): array
    {
        $left  = [];
        $right = [];

        foreach ($this->data as $value) {
            $absoluteDeviation = abs($value - $this->median);

            if ($value <= $this->median) {
                $left[] = $absoluteDeviation;
            }

            if ($value >= $this->median) {
                $right[] = $absoluteDeviation;
            }
        }

        $mad = [
            'left'  => collect($left)->median(),
            'right' => collect($right)->median(),
        ];

        if ($mad['left'] === 0 || $mad['right'] === 0) {
            throw new \Exception('MAD is 0');
        }

        return $mad;
    }

    public function outliers(): array
    {
        $outliers  = [];
        $distances = [];

        $doubleMad = $this->doubleMad();

        foreach ($this->data as $key => $value) {
            $mad = ($value <= $this->median) ? $doubleMad['left'] : $doubleMad['right'];

            if ($value === $this->median) {
                $distances[$key] = 0;
            } else {
                $distances[$key] = abs($value - $this->median) / $mad;
            }
        }

        foreach ($distances as $index => $distance) {
            if ($distance > $this->cutoff) {
                $outliers[$index] = $this->data[$index];
            }
        }

        return $outliers;
    }
}
