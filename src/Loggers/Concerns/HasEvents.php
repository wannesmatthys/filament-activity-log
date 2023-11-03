<?php

namespace Noxo\FilamentActivityLog\Loggers\Concerns;

trait HasEvents
{
    public static ?array $events = [
        'created',
        'updated',
        'deleted',
        'restored',
    ];

    public static function registerEvents(): void
    {
        foreach (static::$events as $event) {
            if (! method_exists(static::$model, $event)) {
                continue;
            }

            static::$model::{$event}(function ($model) use ($event) {
                if ($event === 'updated') {
                    $old = $model::make($model->getOriginal());
                    $new = $model::make($model->getAttributes());
                    $old->id = $model->id;
                    static::make($old, $new)->updated();
                } else {
                    static::make($model)->{$event}();
                }
            });
        }
    }

    /**
     * Log when a model is created.
     */
    public function created(): void
    {
        $attributes = [];

        foreach ($this->getFields() as $field) {
            $value = $field->getStorableValue($this->newModel);

            if (! empty($value)) {
                $attributes[$field->name] = $value;
            }
        }

        $this->log(
            ['old' => [], 'attributes' => $attributes],
            event: 'created',
        );
    }

    /**
     * Log when a model is updated.
     */
    public function updated(): void
    {
        $old = [];
        $new = [];

        foreach ($this->getFields() as $field) {
            $beforeValue = $field->getStorableValue($this->oldModel);
            $afterValue = $field->getStorableValue($this->newModel);

            if ($beforeValue !== $afterValue) {
                $old[$field->name] = $beforeValue;
                $new[$field->name] = $afterValue;
            }
        }

        $this->logIf(
            $old !== $new,
            ['old' => $old, 'attributes' => $new],
            event: 'updated',
        );
    }

    /**
     * Log when a model is deleted.
     */
    public function deleted(): void
    {
        $attributes = [];

        foreach ($this->getFields() as $field) {
            $value = $field->getStorableValue($this->newModel);

            if (! empty($value)) {
                $attributes[$field->name] = $value;
            }
        }

        $this->log(
            ['old' => [], 'attributes' => $attributes],
            event: 'deleted',
        );
    }

    /**
     * Log when a model is restored.
     */
    public function restored(): void
    {
        $this->log(
            ['old' => [], 'attributes' => []],
            event: 'restored',
        );
    }
}
