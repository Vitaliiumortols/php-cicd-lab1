<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../app/Services/DataProcessor.php';

class DataProcessorTest extends TestCase
{
    public function test_sum_natural_numbers(): void
    {
        $this->assertSame(40, sumNaturalNumbers([3, 5, 7, 10, 15]));
    }

    public function test_last_three_words(): void
    {
        $text = 'У нашому магазині можна купити новий iPhone';

        $this->assertSame('купити новий iPhone', lastThreeWords($text));
    }

    public function test_shuffle_keeps_all_words(): void
    {
        $originalWords = explode(' ', 'У магазині є новий iPhone');
        $shuffledWords = explode(' ', shuffleWords('У магазині є новий iPhone'));
        sort($originalWords);
        sort($shuffledWords);

        $this->assertSame($originalWords, $shuffledWords);
    }
}
