<?php

namespace locky42\adminlte\dropdownTable\helpers;

use Closure;
use Opis\Closure\SerializableClosure;

class FormatHelper
{
    public static function serialize($data): string
    {
        if ($data instanceof Closure) {
            return serialize(new SerializableClosure($data));
        } elseif (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = self::serialize($value);
            }
            return serialize($result);
        } else {
            return serialize($data);
        }
    }

    public static function unserialize($data)
    {
        $result = unserialize($data);
        if ($result instanceof SerializableClosure) {
            $result = $result->getClosure();
        } elseif (is_array($result)) {
            foreach ($result as $key => $value) {
                $result[$key] = self::unserialize($value);
            }
        }
        return $result;
    }
}
