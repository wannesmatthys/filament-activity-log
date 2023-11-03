<?php

namespace Noxo\FilamentActivityLog\Fields\Concerns\Types;

use Illuminate\Database\Eloquent\Model;

trait Media
{
    public string $rounded = 'circle';

    public bool $gallery = false;

    public function media(bool $gallery = false): static
    {
        $this->type('media');
        $this->view('image');
        $this->relation();
        $this->gallery = $gallery;

        if ($gallery) {
            $this->square();
        }

        $this->resolveUsing(function (Model $model): mixed {
            $relation = $model->{$this->name};

            if ($this->gallery) {
                return $relation->pluck('original_url')->toArray();
            }

            return $relation[0]['original_url'] ?? null;
        });

        return $this;
    }

    public function circle(): static
    {
        $this->rounded = 'circle';

        return $this;
    }

    public function square(): static
    {
        $this->rounded = 'square';

        return $this;
    }
}
