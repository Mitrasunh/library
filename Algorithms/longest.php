<?php

function longest($sentence)
{
    $words = explode(' ', $sentence);

    $longestWord = '';
    foreach ($words as $word) {
        if (strlen($word) > strlen($longestWord)) {
            $longestWord = $word;
        }
    }

    return $longestWord . ' (' . strlen($longestWord) . ' karakter)';
}

$sentence = "Saya sangat senang mengerjakan soal algoritma";
echo longest($sentence);
