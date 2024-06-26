<?php

namespace locky42\adminlte\dropdownTable\helpers;

use locky42\adminlte\dropdownTable\DropdownTable;

class TreeHelper
{
    /**
     * @param DropdownTable|null $item
     * @return int[]
     */
    public static function getParentsIds(?DropdownTable $item): array
    {
        $array = self::getParents($item);
        $ids = [0];
        foreach (array_reverse($array) as $value) {
            $ids[] = $value['id'];
        }

        return $ids;
    }

    /**
     * @param DropdownTable|null $item
     * @return array
     */
    protected static function getParents(?DropdownTable $item): array
    {
        $result = [];
        if ($item?->parent && $id = $item->currentId) {
            $result[] = [
                'parent' => $item->parent->getCurrentId(),
                'id' => $id,
            ];

            $result = array_merge($result, self::getParents($item->parent));
        } elseif ($id = $item?->getCurrentId()) {
            $result[] = [
                'parent' => 0,
                'id' => $id,
            ];
        }

        return $result;
    }
}
