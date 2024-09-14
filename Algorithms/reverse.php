<?php

function reverse($input)
{
    $letters = preg_replace('/[^a-zA-Z]/', '', $input);
    $numbers = preg_replace('/[^0-9]/', '', $input);

    $reversedLetters = strrev($letters);

    return $reversedLetters . $numbers;
}
$input = "NEGIE1";
echo reverse($input);
