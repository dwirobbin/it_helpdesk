<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    protected static function bootSluggable()
    {
        static::saving(function (self $model) {
            $source = $model->sluggable['source'];
            $slugColumn = $model->sluggable['slug_column'] ?? 'slug';

            // Jika nilai kolom referensi (misalnya 'name') tidak berubah, gunakan slug yang ada
            if (!$model->isDirty($source) && $model->{$slugColumn}) {
                $model->{$slugColumn} = $model->getOriginal($slugColumn);
                return;
            }

            $model->{$slugColumn} = $model->generateSlug($model->{$source}, $slugColumn, $model->getKey());
        });
    }

    protected function generateSlug(string $source, string $column = 'slug'): string
    {
        // Create a slug from the source using Laravel's Str::slug method
        $slug = strtolower(Str::slug($source));

        $count = 2;
        $originalSlug = $slug;

        do {
            // Check if the generated slug already exists in the specified column of the model's table
            $checkSlug = $this->query()->where($column, $slug)->exists();

            // If the slug already exists, append a number to make it unique
            if ($checkSlug) {
                // Append a number to the original source to create a new slug
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
        } while ($checkSlug);

        // If the slug is unique, return it
        return $slug;
    }
}
