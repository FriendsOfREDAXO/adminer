<?php

class rex_adminer extends Adminer
{
    function credentials()
    {
        $db = rex::getProperty('db')[1];
        return [$db['host'], $db['login'], $db['password']];
    }

    function database()
    {
        return rex::getProperty('db')[1]['name'];
    }

    function databases($flush = true)
    {
        return [];
    }

    function databasesPrint($missing)
    {
    }
}
