<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait EnumToArray
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_combine(self::UcWordsNames(), self::values());
    }

    public static function ucWordsNames(): array
    {
        $names  = self::names();
        $ucWordsNames = [];
        foreach ($names as $name) {
            $label = $name;
            if (Str::contains($label, '_')) {
                $label = Str::replace('_', ' ', $label);
            }

            $ucWordsNames[] = Str::title($label);
        }

        return $ucWordsNames;
    }

    public static function forSelect(): array
    {
        $options = [];

        for ($i = 0; $i <= count(self::cases()); $i++) {
            if ($i === count(self::cases())) {
                for ($j = 1; $j <= count(self::cases()); $j++) {
                    $options[$j - 1]['id'] = $j;
                }
                break;
            }

            $options[$i] = [
                'label' => self::ucWordsNames()[$i],
                'value' => self::values()[$i],
            ];
        }

        return $options;
    }
}
