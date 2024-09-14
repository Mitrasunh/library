<?php

function countOccurrences($input, $query)
{
    $inputCount = array_count_values($input);

    $result = [];
    foreach ($query as $word) {
        $result[] = isset($inputCount[$word]) ? $inputCount[$word] : 0;
    }

    return $result;
}

$input = ['xc', 'dz', 'bbb', 'dz'];
$query = ['bbb', 'ac', 'dz'];
echo '[' . implode(',', countOccurrences($input, $query)) . ']';
