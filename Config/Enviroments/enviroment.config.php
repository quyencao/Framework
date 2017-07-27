<?php

$config = "development.config.php";

foreach (scandir(__DIR__) as $item)
{
    if ($item == 'enviroment.config.php' || $item == 'development.config.php' || strpos($item, '.config.php') === false)
    {
        continue;
    }

    $config = $item;
    break;
}

$exports = str_replace('.config.php', '', $config);
