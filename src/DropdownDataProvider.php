<?php

namespace locky42\adminlte\dropdownTable;

use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

class DropdownDataProvider extends ActiveDataProvider
{
    /**
     * @var int $counter
     */
    private static int $counter = 0;

    /**
     * @return void
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        if ($this->id === null) {
            $this->id = 'dp-' . self::$counter;
            self::$counter++;
        }
        parent::init();
    }
}
