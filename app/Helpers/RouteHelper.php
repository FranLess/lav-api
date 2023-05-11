<?php

namespace App\Helpers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RouteHelper
{

    public static function includeRouteFiles($folder)
    {
        //iterate through th folder path
        $dirIterator = new RecursiveDirectoryIterator($folder);
        /** @var RecursiveDirectoryIterator | \RecursiveIteratorIterator $it */
        $it = new RecursiveIteratorIterator($dirIterator);

        //require the file in each iteration

        while ($it->valid()) {
            if (
                !$it->isDot()
                && $it->isFile()
                && $it->isReadable()
                && $it->current()->getExtension() === 'php'
            ) {
                require $it->current()->getPathname();
            }
            $it->next();
        }
    }
}
