<?php

//namespace App\Helpers; // define Helper scope


if(!function_exists('decodeHTMLEntity')) {

    function decodeHTMLEntity($data)
    {
        return html_entity_decode($data, ENT_QUOTES, 'utf-8');
    }
}

