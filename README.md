AdminLte3 dropdown table
========================
AdminLte3 dropdown table

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist locky42/yii2-adminlte-dropdown-table "*"
```

or add `"locky42/yii2-adminlte-dropdown-table": "*"` to the require section of your `composer.json` file.

## Usage

### Static

Once the extension is installed, simply use it in your code by  :

```php
<?= \locky42\adminlte\dropdownTable\AutoloadExample::DropdownTable(
    'dataProvider' => $dataProvider,
    'ariaExpanded' => false,
    'relations' => [
        'subObjectFirstLevel' => [
            'title' => 'SubTable title',
            'ariaExpanded' => false,
            'columns' => [
                [
                    'format' => 'html',
                    'label' => '#',
                    'attribute' => 'id',
                ],
                ...
            ],
            'relations' => [
                'subObjectSecondLevel' => [
                    'title' => 'SubObjects title',
                    'relations' => [
                        ...
                    ],
                    'columns' => [
                        ...
                    ],
                ],
            ]
        ],
    ],
    'columns' => [
        [
            'format' => 'html',
            'label' => '#',
            'attribute' => 'id',
        ],
        ...
        'urlCreator' => function ($action, $model, $key, $index, $column) {
            return Url::toRoute(['/{url}}/' . $action, 'id' => $model->id]);
        },
        'buttonOptions' => ['target' => '_blank'],
    ],
]); ?>
```

In your controller $dataProvider must be a `locky42\adminlte\dropdownTable\DropdownDataProvider`
```php
use \locky42\adminlte\dropdownTable\DropdownDataProvider;

...

$dataProvider = new DropdownDataProvider([
    'query' => $query,
    ...
]);
```

### Ajax

Once the extension is installed, simply use it in your code by  :

```php
<?= \locky42\adminlte\dropdownTable\AutoloadExample::DropdownTable(
    'dataProvider' => $dataProvider,
    'ajax' => true,
    'ajaxUrl' => '{url}',
    'relations' => [
        'subObjectFirstLevel' => [
            'title' => 'SubTable title',
            'ajax' => true,
            'ajaxUrl' => '{url}',
            ...
        ],
    ],
    'columns' => [
        ...
    ],
]); ?>
```

You must have a controller with the extension `locky42\adminlte\dropdownTable\controllers\rest\TableController`.
This controller is used to url creation and data rendering.
