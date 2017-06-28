<?php

namespace TKS\traits;

/**
 * 
 */
trait LabelHelper
{
    public static $keyField;

    public static function labelFilterWithDb($key, array &$condition = null)
    {
        $field = self::$keyField ?? 'label';
        if (!self::validateClass()) {
            return false;
        }
        $condition = ($condition ?: []);
        if (is_array($key)) {
            foreach($key as $k) {
                return self::labelFilter($k);
            }
        } elseif (is_string($key)) {
            $condition[] = "{$field} like '%{$key}%'";
            return true;
        }
    }

    private static function validateClass(): bool
    {
        return is_subclass_of(get_called_class(), 'Business\\AbstractModel');
    }
}
