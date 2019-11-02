<?php

/**
 * Fonction qui retourne une chaîne de caractères aléatoire de longueur n.
 * 
 * @param int           $n                  -       Longueur de la chaîne a generer
 * 
 * @return string
 */
function randomString($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $n; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}





?>