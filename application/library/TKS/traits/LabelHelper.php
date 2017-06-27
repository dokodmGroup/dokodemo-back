<?php

namespace TKS\traits;

/**
 * 
 */
trait LabelHelper
{
    public static function hello()
    {
        if (!self::validateClass()) {
            return;
        }
        echo 'World';
    }

    private static function validateClass(): bool
    {
        return is_subclass_of(get_called_class(), 'Business\\AbstractModel');
    }
}
