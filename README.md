# Laravel Userstamps

<p align="center">
   <a href="https://packagist.org/packages/dalisoft/userstamps">
        <img src="https://poser.pugx.org/dalisoft/userstamps/v/stable.svg" alt="Latest Stable Version">
    </a>
     <a href="https://packagist.org/packages/dalisoft/userstamps">
        <img src="https://poser.pugx.org/dalisoft/userstamps/license.svg" alt="License">
    </a>
    <a href="https://packagist.org/packages/dalisoft/userstamps">
        <img src="https://poser.pugx.org/dalisoft/userstamps/d/total.svg" alt="Total Downloads">
    </a>
</p>

## About

Laravel Userstamps is a Laravel package for your Eloquent Model users fields: `created_by`, `updated_by` and `deleted_by`. This package automatically inserts/updates an user id on your table on who created, last updated and deleted the record.

When using the Laravel `SoftDeletes` trait, a `deleted_by` colummn is also handled by this package.

## Installation

This package requires Laravel 5.2 or later running on PHP 5.6 or higher.

This package can be installed using composer:

````
composer require dalisoft/userstamps
````

## Configuration
Register the ServiceProvider in your config/app.php service provider list.

config/app.php
````
return [
    //other
    'providers' => [
        //other
        DaLiSoft\Userstamps\UserStampServiceProvider::class,
    ];
];
````

## Usage
### On Migrations
Your model will need to include a `created_by` and `updated_by` column, defaulting to `null`.
If using the Laravel `SoftDeletes` trait, it will also need a `deleted_by` column.

The column type should match the type of the ID colummn in your user's table. In Laravel <= 5.7 this defaults to `unsignedInteger`. For Laravel >= 5.8 this defaults to `unsignedBigInteger`.

You can use the Blueprint method `userstamps()` and add created_by, updated_by and deleted_by.

An example migration with Blueprint method:
```php
Schema::create('mytable', function (Blueprint $table) {

    $table->userstamps();
});
```

An example migration add user stamp field:
```php
Schema::create('mytable', function (Blueprint $table) {

    $table->unsignedInteger('created_by')->nullable();
    $table->unsignedInteger('updated_by')->nullable();
    $table->unsignedInteger('deleted_by')->nullable();
});
```

An example migration drop auditable columns:
```php
Schema::create('mytable', function (Blueprint $table) {

    $table->dropUserstamps();
});
```

### Attaching to Model
You can now load the trait within your model, and userstamps will automatically be maintained:

```php
use DaLiSoft\Userstamps\Userstamps;

class Foo extends Model {

    use Userstamps;
}
```

### custom attributes
Optionally, should you wish to override the names of the `created_by`, `updated_by` or `deleted_by` columns, you can do so by setting the appropriate class constants on your model. Ensure you match these column names in your migration.
You can also set the name of the `display_user` column that you want to return in the methods, by default it returns name.

```php
use DaLiSoft\Userstamps\Userstamps;

class Foo extends Model {

    use Userstamps;
    const CREATED_BY = 'alt_created_by';
    const UPDATED_BY = 'alt_updated_by';
    const DELETED_BY = 'alt_deleted_by';
    const DISPLAY_USER = 'email';
}
```

When using this trait, helper relationships are available to let you retrieve the user who created, updated and deleted (when using the Laravel `SoftDeletes` trait) your model.

```php
$model->creator; // the user who created the model
$model->editor; // the user who last updated the model
$model->destroyer; // the user who deleted the model
```

Methods are also available to temporarily stop the automatic maintaining of userstamps on your models:

```php
$model->stopUserstamping(); // stops userstamps being maintained on the model
$model->startUserstamping(); // resumes userstamps being maintained on the model
```

There are also attributes available to get the name / mail / ... of the creator, editor and destroyer user in their models:

```php
$model->created_by_user; // creator username in the model
$model->updated_by_user; // editor username in the model
$model->deleted_by_user; // destroyer username in the model
```

## Workarounds

This package works by by hooking into Eloquent's model event listeners, and is subject to the same limitations of all such listeners.

When you make changes to models that bypass Eloquent, the event listeners won't be fired and userstamps will not be updated.

Commonly this will happen if bulk updating or deleting models, or their relations.

In this example, model relations are updated via Eloquent and userstamps **will** be maintained:

```php
$model->foos->each(function ($item) {
    $item->bar = 'x';
    $item->save();
});
```

However in this example, model relations are bulk updated and bypass Eloquent. Userstamps **will not** be maintained:

```php
$model->foos()->update([
    'bar' => 'x',
]);
```

As a workaroud to this issue two helper methods are available - `updateWithUserstamps` and `deleteWithUserstamps`. Their behaviour is identical to `update` and `delete`, but they ensure the `updated_by` and `deleted_by` properties are maintained on the model.

 You generally won't have to use these methods, unless making bulk updates that bypass Eloquent events.

 In this example, models are bulk updated and userstamps **will not** be maintained:

```php
$model->where('name', 'foo')->update([
    'name' => 'bar',
]);
```

However in this example, models are bulk updated using the helper method and userstamps **will** be maintained:

```php
$model->where('name', 'foo')->updateWithUserstamps([
    'name' => 'bar',
]);
```

## References

This project was developed using the <a href="https://github.com/WildsideUK/Laravel-Userstamps">WILDSIDE</a> project.

I have added new qualities to the package, such as getting the name or email of the user who created, updated and unregistered.

The functionality was also added to update the user in a parent table when creating, modifying, or deleting a record in the child table.

## License

This open-source software is licensed under the [MIT license](https://opensource.org/licenses/MIT).
