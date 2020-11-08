[![Latest Stable Version](https://poser.pugx.org/arrilot/laravel-data-anonymization/v/stable.svg)](https://packagist.org/packages/arrilot/laravel-data-anonymization/)
[![Total Downloads](https://img.shields.io/packagist/dt/arrilot/laravel-data-anonymization.svg?style=flat)](https://packagist.org/packages/Arrilot/laravel-data-anonymization)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/arrilot/laravel-data-anonymization/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/arrilot/laravel-data-anonymization/)

# Laravel Data Anonymization

* This is a bridge package for a full integration of [arrilot/data-anonymization](https://github.com/arrilot/data-anonymization) into Laravel framework.

## Installation

1. ```composer require arrilot/laravel-data-anonymization```

2. Add `"Database\\Anonymization\\": "database/anonymization/",` to `composer.json -> autoload -> psr-4`

4. `php artisan anonymization:install`


## Usage

The package is designed to be as much consistent with Laravel built-in seeders as possible.

### Bootstrapping

`php artisan anonymization:install` creates two files:

1) `database/anonymization/DatabaseAnonymizer.php`

```php
<?php

namespace Database\Anonymization;

use Arrilot\LaravelDataAnonymization\AbstractAnonymizer;

class DatabaseAnonymizer extends AbstractAnonymizer
{
    /**
     * Run the database anonymization.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableAnonymizer::class);
    }
}

```

2) `database/anonymization/UserTableAnonymizer.php`

```php
<?php

namespace Database\Anonymization;

use Arrilot\DataAnonymization\Blueprint;
use Arrilot\LaravelDataAnonymization\AbstractAnonymizer;
use Faker\Generator as Faker;

class UsersAnonymizer extends AbstractAnonymizer
{
    /**
     * Run the database anonymization.
     *
     * @return void
     */
    public function run()
    {
        // For more info about this part read here https://github.com/arrilot/data-anonymization
        $this->table('users', function (Blueprint $table) {

            $table->column('email')->replaceWith(function(Faker $faker) {
                return $faker->unique()->email;
            });

            $table->column('name')->replaceWith('John Doe');
        });
    }
}

```

`DatabaseAnonymizer` is an entry point into anonymization. It runs other anonymizers.
`UsersAnonymizer` is a useful built-in example. You can modify it and create other anonymizers for other tables using generator.

### Generator command

`php artisan make:anonymizer AccountsAnonymizer`. Similar to `make:seeder`

### Running the anonymization

Anonymization is performed using `php artisan db:anonymize` command.
Its signature is identical with `db:seed` command.

