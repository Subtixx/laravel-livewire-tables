<?php

namespace Rappasoft\LaravelLivewireTables\Views;

use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Traits\Configuration\ColumnConfiguration;
use Rappasoft\LaravelLivewireTables\Views\Traits\Helpers\ColumnHelpers;
use Rappasoft\LaravelLivewireTables\Views\Traits\Helpers\RelationshipHelpers;

class Column
{
    use ColumnConfiguration,
        ColumnHelpers,
        RelationshipHelpers;

    protected ?DataTableComponent $component = null;

    // What displays in the columns header
    protected string $title;

    // Act as a unique identifier for the column
    protected string $hash;

    // The columns or relationship location: i.e. name, or address.group.name
    protected ?string $from = null;

    // The underlying columns name: i.e. name
    protected ?string $field = null;

    // The table of the columns or relationship
    protected ?string $table = null;

    // An array of relationships: i.e. address.group.name => ['address', 'group']
    protected array $relations = [];

    protected bool $sortable = false;

    protected mixed $sortCallback = null;

    protected bool $searchable = false;

    protected mixed $searchCallback = null;

    protected bool $collapseOnMobile = false;

    protected bool $collapseOnTablet = false;

    protected bool $collapseAlways = false;

    protected ?string $sortingPillTitle = null;

    protected ?string $sortingPillDirectionAsc = null;

    protected ?string $sortingPillDirectionDesc = null;

    protected bool $eagerLoadRelations = false;

    protected mixed $formatCallback = null;

    protected bool $html = false;

    protected mixed $labelCallback = null;

    protected bool $hidden = false;

    protected bool $selectable = true;

    protected bool $selected = true;

    protected bool $secondaryHeader = false;

    protected mixed $secondaryHeaderCallback = null;

    protected bool $footer = false;

    protected mixed $footerCallback = null;

    protected bool $clickable = true;

    protected ?string $customSlug = null;

    public function __construct(string $title, string $from = null)
    {
        $this->title = trim($title);

        if ($from) {
            $this->from = trim($from);
            $this->hash = md5($this->from);

            if (Str::contains($this->from, '.')) {
                $this->field = Str::afterLast($this->from, '.');
                $this->relations = explode('.', Str::beforeLast($this->from, '.'));
            } else {
                $this->field = $this->from;
            }
        } else {
            $this->field = Str::snake($title);
            $this->hash = md5($this->field);
        }
    }

    /**
     * @return static
     */
    public static function make(string $title, string $from = null): Column
    {
        return new static($title, $from);
    }
}
