<?php

namespace locky42\adminlte\dropdownTable;

use yii\data\ActiveDataProvider;

class DropdownDataProvider extends ActiveDataProvider
{
    /**
     * @var int $counter
     */
    private static $counter = 0;

    /**
     * @return void
     */
    public function init()
    {
        if ($this->id === null) {
            $this->id = 'dp-' . self::$counter;
            self::$counter++;
        }
        parent::init();
    }
}
