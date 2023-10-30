<div class="filament-hidden">

![header](https://raw.githubusercontent.com/noxoua/filament-activity-log/main/.github/resources/header.png)

# Filament Activity Log

[![Latest Version on Packagist](https://img.shields.io/packagist/v/noxoua/filament-activity-log.svg?include_prereleases)](https://packagist.org/packages/noxoua/filament-activity-log)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/noxoua/filament-activity-log/code-style.yml?branch=main&label=Code%20style&style=flat-square)
[![Total Downloads](https://img.shields.io/packagist/dt/noxoua/filament-activity-log.svg)](https://packagist.org/packages/noxoua/filament-activity-log)

* [Introduction](#introduction)
* [Installation](#installation)
* [Activities Page](#activities-page)
  - [Create a page](#create-a-page)
* [Loggers](#loggers)
  - [Logger Class](#logger-class)
  - [Key Features](#key-features)
  - [Create a Logger](#create-a-logger)
  - [Usage Example](#usage-example)
  - [Logger - Events](#logger---events)
    - [Primary events](#primary-events)
    - [Add a custom event](#add-a-custom-event)
  - [Logger - Fields](#logger---fields)
  - [Logger - Field Types](#logger---field-types)
  - [Logger - Field Value Views](#logger---field-value-views)
  - [Logger - Field Translated Keys](#logger---field-translated-keys)
  - [Logger - Usage with relations](#logger---usage-with-relations)
    - [CreateRecord](#createrecord)
    - [EditRecord](#editrecord)
    - [Table actions](#table-actions)
* [Contributing](#contributing)
* [License](#license)

</div>

## Introduction

This package is an add-on for simplified activity logging based on [`spatie/laravel-activitylog`](https://github.com/spatie/laravel-activitylog) package. This package also includes a page for viewing activity logs.

The viewing page was copied from [`pxlrbt/filament-activity-log`](https://github.com/pxlrbt/filament-activity-log) package and slightly modernized. If you only need the page for viewing activity logs, without additional functionality, use this package - [`pxlrbt/filament-activity-log`](https://github.com/pxlrbt/filament-activity-log)


<div class="filament-hidden">

![Screenshot](https://raw.githubusercontent.com/noxoua/filament-activity-log/main/.github/resources/screenshot.png)

</div>

## Installation


Install via Composer.

**Requires PHP 8.0 and Filament 3.0+**

```bash
composer require noxoua/filament-activity-log
```

Optionally, you can publish the `config` file with:

```bash
php artisan vendor:publish --tag="filament-activity-log-config"
```

Optionally, you can publish the `views` file with:

```bash
php artisan vendor:publish --tag="filament-activity-log-views"
```

Optionally, you can publish the `translations` file with:

```bash
php artisan vendor:publish --tag="filament-activity-log-translations"
```


## Activities Page

### Create a page

Create a new page and extends the `ListActivities` class.

```php
<?php

namespace App\Filament\Pages;

use Noxo\FilamentActivityLog\Pages\ListActivities;

class Activities extends ListActivities
{
    //
}
```

## Loggers

### Logger Class

The Logger class is a fundamental component of the "Filament Activity Log" package, responsible for capturing and logging activities related to specific models within your Laravel application. It offers powerful customization options to define precisely which events and data changes should be recorded, making it a flexible and versatile tool for tracking model-related actions.

### Key Features

- **Event Logging**: The Logger class can capture a variety of events related to your models, including record creation, updates, deletions, and restorations. These events are crucial for maintaining an audit trail of activities within your application.

- **Customization**: You can customize each Logger to track only the events and fields that are relevant to your application. This flexibility ensures that you log the data that matters most to your specific use case.

- **Field Types**: The Logger class supports various field types, making it easy to log and display different types of data appropriately. This includes handling dates, times, media files, boolean values, and more.

- **Relation Support**: If your models have relationships with other models, Logger can track and log these related models as well. This is essential for understanding complex data dependencies.

- **Field Value Views**: Logger offers field value views, allowing you to specify how specific fields are displayed in the activity log views. You can use views like "avatar," "image," and "badge" to enhance the user experience.

- **Field Translated Keys**: For user-friendly activity logs, Logger allows you to map field keys to translated keys, ensuring that your logs are easily understandable in different languages.

### Create a Logger

Use the artisan command to create a logger.

```bash
php artisan make:filament-logger User
```

Once `UserLogger` is created, it immediately starts listening to model events.


### Usage Example

Here's a simple example of how to create a Logger for a User model:

```php
use Noxo\FilamentActivityLog\ActivityLogger;
use App\Models\User;

class UserLogger extends ActivityLogger
{
    public static ?string $modelClass = User::class;

    public static ?array $events = [
        // 'created',
        // 'updated',
        'deleted',
        'restored',
    ];

    public static ?array $fields = [
        'name',
        'email',
        'email_verified_at',
        'media',
        'roles',
    ];

    public static ?array $types = [
        'email_verified_at' => 'datetime:Y-m-d',
        'media' => 'media',
        'roles' => 'pluck:name',
    ];

    public static ?array $views = [
        'email_verified_at' => 'badge',
        'media' => 'avatar',
        'roles' => 'badge',
    ];
}
```

### Logger - Events

The Logger class has the `events` property, which is a list of events that will be logged automatically.


```php
public static ?array $events = [
    'created',
    'updated',
    'deleted',
    'restored',
];
```

#### Primary events

```php
// Created
UserLogger::make($record)->created();

// Updated
UserLogger::make($old_record, $new_record)->updated();

// Deleted
UserLogger::make($record)->deleted();

// Restored
UserLogger::make($record)->restored();
```

#### Add a custom event

You can add custom events to your `UserLogger` class to log specific actions or activities in your application. In this example, we've added a custom event called `transaction_deposit`. This event is triggered when a deposit transaction is made.

Here's how to implement a custom event:

1. Define the custom event method within your UserLogger class. In this case, the transaction_deposit method is created.

```php
class UserLogger extends ActivityLogger
{
    // ...

    public function transaction_deposit($transaction): void
    {
        // Define the attributes you want to log for the event
        $attributes = [
            'amount' => $transaction->amount,
            'additional_info' => $transaction->meta['text'] ?? null,
        ];

        // Log the event with the specified attributes and event name
        $this->log(
            ['old' => [], 'attributes' => $attributes],
            event: 'deposit',
        );
    }
}
```

2. To trigger the custom event, you can use the `->transaction_deposit` method. This will log the event with the provided attributes, such as the deposit amount and additional information.

```php
// Trigger the custom event
UserLogger::make($user_record)->transaction_deposit($transaction);
```

### Logger - Fields

The Logger class has the `fields` property where you can specify all fields that need to be logged. You can also specify relations.


```php
public static ?array $fields = [
    'name',
    'email',
    'email_verified_at',
    'media', // <---- relation
    'roles', // <---- relation
];
```

### Logger - Field Types

The Logger class has the `types` property where you can specify fields and how they should be logged.

```php
public static ?array $types = [
    'email_verified_at' => 'datetime:Y-m-d', // format is optional
    'media' => 'media', // <---- spatie media-library
    'roles' => 'pluck:name', // <---- relation
];
```

Available types:
- date
- time
- datetime
- media | media:multiple
- boolean
- only:first_name,last_name
- pluck:first_name
- enum


### Logger - Field Value Views

The Logger class has the `views` property where you can define how to display specific fields in the activity log views.

```php
public static ?array $views = [
    'email_verified_at' => 'badge',
    'media' => 'avatar',
    'roles' => 'badge',
];
```

Available views:
- avatar
- image
- badge

___

<img src="https://raw.githubusercontent.com/noxoua/filament-activity-log/main/.github/resources/screenshot-views.png" width="750">

(Note: _"avatar" and "image" have the same size but a different border-radius._)


### Logger - Field Translated Keys

The Logger class has the `attributeMap` property where you can map field keys to make translations user-friendly.

```php
public static ?array $attributeMap = [
    'name' => 'first_name',
    'media' => 'avatar',
];
```

### Logger - Usage with relations

If you want to log relations as well, you should comment out the `created` and `updated` events and manually add a logger to your filament resource.

```php
public static ?array $events = [
    // 'created',
    // 'updated',
    'deleted',
    'restored',
];
```

#### CreateRecord
```php

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function afterCreate()
    {
        UserLogger::make($this->record)->created();
    }
}

```

#### EditRecord

```php

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public $old_model;

    public function beforeValidate()
    {
        $this->old_model = clone $this->record->load('roles', 'media');
    }

    public function afterSave()
    {
        $new_model = $this->record->load('roles', 'media');
        UserLogger::make($this->old_model, $new_model)->updated();
    }
}

```

#### Table actions

```php

->actions([
    Tables\Actions\Action::make('verify_email')
        ->action(function ($record) {

            UserLogger::make($record)
                ->through(fn ($record) => $record->markEmailAsVerified())
                ->updated();

        }),
])

```

### Logger - Process Custom Field or Complex Relation

In some cases, you may have custom fields or complex relations within your models that you want to log in a structured way. To achieve this, you can define a method in your logger, such as the `processMeta` method shown in the example:

```php
class UserLogger extends ActivityLogger
{
    // ...

    public function processMeta(Model $model): mixed
    {
        $attributes = [];

        // Iterate through the 'meta' attribute of the model
        foreach ($model->meta as $value) {
            // Define the custom attributes you want to log
            $attributes[$value['custom_name']] = $value['custom_value'];
        }

        // Return the processed attributes
        return $attributes;
    }
}
```
In the example, the method name follows the convention of prefixing with `process` and using the field name `Meta` to indicate that it is processing the `meta` field of the model. It iterates through the `meta` attribute and extracts specific custom attributes, storing them in the `$attributes` array. These processed attributes can then be logged as part of an activity event.

```php
// process - is a prefix
// Meta - field name `ucfirst`
```

## Contributing

Please see [CONTRIBUTING](https://raw.githubusercontent.com/noxoua/filament-activity-log/main/.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
