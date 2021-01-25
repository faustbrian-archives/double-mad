<?php

declare(strict_types=1);

use Konceiver\DoubleMadOutlier\DoubleMadOutlier;

it('should end up with the median as MAD', function (): void {
    $subject = new DoubleMadOutlier([1, 2, 3, 4, 5, 6, 7, 8, 9]);

    expect($subject->doubleMad())->toBe(['left' => 2, 'right' => 2]);
});

it('should end up with the average as MAD', function (): void {
    $subject = new DoubleMadOutlier([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

    expect($subject->doubleMad())->toBe(['left' => 2.5, 'right' => 2.5]);
});

it('should throw an exception if the MAD is 0', function (): void {
    $subject = new DoubleMadOutlier([1, 1, 1, 1, 1, 1, 1, 1, 1]);

    $subject->doubleMad();
})->throws(\Exception::class);

it('should find all outliers with the default cutoff', function (): void {
    $subject = new DoubleMadOutlier([30, 10, 4, 7, 4, 5, 5, 7, 8, 1, 16, 4, 5, 5]);

    expect($subject->outliers())->toBe([9 => 1, 10 => 16, 0 => 30]);
});

it('should find all outliers with a small cutoff', function (): void {
    $subject = new DoubleMadOutlier([30, 10, 4, 7, 4, 5, 5, 7, 8, 1, 16, 4, 5, 5], 1);

    expect($subject->outliers())->toBe([9 => 1, 2 => 4, 4 => 4, 11 => 4, 8 => 8, 1 => 10, 10 => 16, 0 => 30]);
});

it('should find all outliers with a large cutoff', function (): void {
    $subject = new DoubleMadOutlier([30, 10, 4, 7, 4, 5, 5, 7, 8, 1, 16, 4, 5, 5], 10);

    expect($subject->outliers())->toBe([0 => 30]);
});
