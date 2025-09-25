<?php
// cipher.php

function encryptCaesar($text, $shift) {
    $result = "";
    $text = strtolower($text);

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) {
            $shifted = ord($char) + $shift;
            if ($shifted > ord('z')) {
                $shifted -= 26;
            }
            $result .= chr($shifted);
        } elseif (ctype_digit($char)) {
            $shifted = ord($char) + $shift;
            if ($shifted > ord('9')) {
                $shifted -= 10;
            }
            $result .= chr($shifted);
        } else {
            $result .= $char;
        }
    }
    return $result;
}

function decryptCaesar($text, $shift) {
    $result = "";
    $text = strtolower($text);

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) {
            $shifted = ord($char) - $shift;
            if ($shifted < ord('a')) {
                $shifted += 26;
            }
            $result .= chr($shifted);
        } elseif (ctype_digit($char)) {
            $shifted = ord($char) - $shift;
            if ($shifted < ord('0')) {
                $shifted += 10;
            }
            $result .= chr($shifted);
        } else {
            $result .= $char;
        }
    }
    return $result;
}
