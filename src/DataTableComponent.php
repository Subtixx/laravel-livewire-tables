<?php

namespace Rappasoft\LaravelLivewireTables;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\Exceptions\DataTableConfigurationException;
use Rappasoft\LaravelLivewireTables\Traits\ComponentUtilities;
use Rappasoft\LaravelLivewireTables\Traits\WithBulkActions;
use Rappasoft\LaravelLivewireTables\Traits\WithColumns;
use Rappasoft\LaravelLivewireTables\Traits\WithColumnSelect;
use Rappasoft\LaravelLivewireTables\Traits\WithData;
use Rappasoft\LaravelLivewireTables\Traits\WithDebugging;
use Rappasoft\LaravelLivewireTables\Traits\WithEvents;
use Rappasoft\LaravelLivewireTables\Traits\WithFilters;
use Rappasoft\LaravelLivewireTables\Traits\WithFooter;
use Rappasoft\LaravelLivewireTables\Traits\WithLoadingPlaceholder;
use Rappasoft\LaravelLivewireTables\Traits\WithPagination;
use Rappasoft\LaravelLivewireTables\Traits\WithRefresh;
use Rappasoft\LaravelLivewireTables\Traits\WithReordering;
use Rappasoft\LaravelLivewireTables\Traits\WithSearch;
use Rappasoft\LaravelLivewireTables\Traits\WithSecondaryHeader;
use Rappasoft\LaravelLivewireTables\Traits\WithSorting;

abstract class DataTableComponent extends Component
{
    use ComponentUtilities,
        WithBulkActions,
        WithColumns,
        WithColumnSelect,
        WithData,
        WithDebugging,
        WithEvents,
        WithFilters,
        WithFooter,
        WithLoadingPlaceholder,
        WithPagination,
        WithRefresh,
        WithReordering,
        WithSearch,
        WithSecondaryHeader,
        WithSorting;

    /** @phpstan-ignore-next-line */
    protected $listeners = [
        'refreshDatatable' => '$refresh',
        'setSort' => 'setSortEvent',
        'clearSorts' => 'clearSortEvent',
        'setFilter' => 'setFilterEvent',
        'clearFilters' => 'clearFilterEvent',
    ];

    /**
     * Returns a unique id for the table, used as an alias to identify one table from another session and query string to prevent conflicts
     */
    protected function generateDataTableFingerprint(): string
    {
        $className = str_split(static::class);
        $crc32 = sprintf('%u', crc32(serialize($className)));

        return base_convert($crc32, 10, 36);
    }

    /**
     * Runs on every request, immediately after the component is instantiated, but before any other lifecycle methods are called
     */
    public function boot(): void
    {
        //
    }

    /**
     * Runs on every request, after the component is mounted or hydrated, but before any update methods are called
     */
    public function booted(): void
    {
        // Call the configure() method
        $this->configure();

        //Sets up the Builder Instance
        $this->setBuilder($this->builder());

        // Sets Columns
        $this->setColumns();

        // Make sure a primary key is set
        if (! $this->hasPrimaryKey()) {
            throw new DataTableConfigurationException('You must set a primary key using setPrimaryKey in the configure method.');
        }
    }

    /**
     * Set any configuration options
     */
    abstract public function configure(): void;

    /**
     * The array defining the columns of the table.
     */
    abstract public function columns(): array;

    /**
     * The base query.
     */
    public function builder(): Builder
    {
        if ($this->hasModel()) {
            return $this->getModel()::query()->with($this->getRelationships());
        }

        throw new DataTableConfigurationException('You must either specify a model or implement the builder method.');
    }

    /**
     * The view to add any modals for the table, could also be used for any non-visible html
     */
    public function customView(): string
    {
        return 'livewire-tables::stubs.custom';
    }

    public function render(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $this->setupColumnSelect();
        $this->setupPagination();
        $this->setupSecondaryHeader();
        $this->setupFooter();
        $this->setupReordering();

        return view('livewire-tables::datatable')
            ->with([
                'columns' => $this->getColumns(),
                'rows' => $this->getRows(),
                'customView' => $this->customView(),
            ]);
    }
}
