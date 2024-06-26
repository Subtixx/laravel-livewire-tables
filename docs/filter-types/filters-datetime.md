---
title: DateTime Filters
weight: 4
---

## DateTime Filters

DateTime filters are HTML datetime-local elements and act the same as date filters.

```php
use Rappasoft\LaravelLivewireTables\Views\Filters\DateTimeFilter;

public function filters(): array
{
    return [
        DateTimeFilter::make('Verified From'),
    ];
}
```

DateTime filters have configs to set min and max, and to set the format for the Filter Pills

```php
public function filters(): array
{
    return [
        DateTimeFilter::make('Verified From')
            ->config([
                'min' => '2022-11-31 00:00:00',
                'max' => '2022-12-31 05:00:00',
                'pillFormat' => 'd M Y - H:i',
            ])
    ];
}
```

DateTime filters also support the setFilterDefaultValue() method, which must be a valid datetime in the "Y-m-dTH:i" format.  This will apply as a default until removed.
```php
public function filters(): array
{
    return [
        DateTimeFilter::make('Verified From')
            ->config([
                'min' => '2022-11-31 00:00:00',
                'max' => '2023-12-31 05:00:00',
                'pillFormat' => 'd M Y - H:i',
            ])
            ->setFilterDefaultValue('2023-07-07T06:27')
    ];
}
```
                    

