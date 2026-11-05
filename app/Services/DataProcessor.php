<?php

declare(strict_types=1);

// Варіант 15: таблиця 3.2, задача 6.
function sumNaturalNumbers(array $numbers): int
{
    return array_sum($numbers);
}

// Таблиця 3.3, задача 3.
function lastThreeWords(string $text): string
{
    $words = preg_split('/\s+/u', trim($text));
    $lastWords = array_slice($words, -3);

    return implode(' ', $lastWords);
}

// Таблиця 3.4, задача 3.
function shuffleWords(string $text): string
{
    $words = preg_split('/\s+/u', trim($text));
    shuffle($words);

    return implode(' ', $words);
}
