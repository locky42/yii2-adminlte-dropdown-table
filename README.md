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
    'custom_content' => 'custom content',
    'relations' => [
        'subObjectFirstLevel' => [
            'title' => 'SubTable title',
            'custom_content' => 'custom content',
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
    'custom_content' => 'custom content',
    'relations' => [
        'subObjectFirstLevel' => [
            'title' => 'SubTable title',
            'ajax' => true,
            'ajaxUrl' => '{url}',
            'custom_content' => 'custom content',
            ...
        ],
    ],
    'columns' => [
        ...
    ],
]); ?>
```

## Other imformation

You must have a controller with the extension `locky42\adminlte\dropdownTable\controllers\rest\TableController`.
This controller is used to url creation and data rendering.

Custom content is displayed after the title.
If you have links in your custom content, you must add the class `no-ajax` to the links.
