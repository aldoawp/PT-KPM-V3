<?php

namespace App\Http\Controllers\Dashboard\Enums;

enum Carts: string
{
    case Sales = 'cart-sales';
    case Restock = 'cart-restock';
    case Return = 'cart-return';

    public static function getFromPath($path)
    {
        $path = explode('/', $path)[1];

        return match ($path) {
            'sales' => self::Sales,
            'restock' => self::Restock,
            'return' => self::Return ,
        };
    }
}
