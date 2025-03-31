<?php

if (!function_exists("generateUUID")) {
    function generateUUID()
    {
        return strtoupper(bin2hex(random_bytes(8)));
    }
}