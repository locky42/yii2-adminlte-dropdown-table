AdminLte3 dropdown table
========================
AdminLte3 dropdown table

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist locky42/yii2-adminlte-dropdown-table "*"
```

or add

```
"locky42/yii2-adminlte-dropdown-table": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \locky42\adminlte\dropdownTable\AutoloadExample::DropdownTable(
    'dataProvider' => $dataProvider,
    'ariaExpanded' => false,
    'relations' => [
        'auReleaseNames' => [
            'columns' => [
                ...
            ],
            'relations' => [
                ...
            ]
        ],
    ],
    'columns' => [
        [
            'format' => 'html',
            'label' => '#',
            'attribute' => 'id',
        ],
        'name',
        [
            'label' => 'Status',
            'attribute' => 'status',
            'format' => 'html',
            'value' => function ($data) {
                return $data->status ? 'Enable' : 'Disable';
            },
        ],
        [
            'class' => ActionColumn::class,
            'urlCreator' => function ($action, $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
            }
        ],
    ],
); ?>
```