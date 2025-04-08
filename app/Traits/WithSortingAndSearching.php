<?php

namespace App\Traits;

trait WithSortingAndSearching
{
    public string $search = ''; // Search term
    public ?string $sortBy = null; // Column to sort by
    public ?string $sortDirection = null; // Sort direction (asc/desc)

    /**
     * Handle search updates and reset pagination.
     *
     * @return void
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Clear a specific filter applied to the current instance.
     *
     * @param string $filter The name of the filter to be cleared.
     *
     * @return void
     */
    public function clearFilter(string $filter): void
    {
        if ($filter === 'search') {
            $this->search = '';
        }
        if ($filter === 'sort') {
            $this->sortBy = '';
            $this->sortDirection = '';
        }
    }


        /**
     * Sort by the given column. Toggles between asc/desc.
     *
     * @param string $column
     * @return void
     */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Clear the sorting (essentially reset to default or no sorting).
     *
     * @return void
     */
    public function clearSort(): void
    {
        $this->sortBy = null;
        $this->sortDirection = null;
    }

    /**
     * Apply search filters to a query.
     *
     * @param $query
     * @return mixed
     */
    /*protected function applySearchFilters($query): mixed
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('email', 'like', '%' . $this->search . '%')
                ->orWhere('name', 'like', '%' . $this->search . '%')
                ->orWhere('message', 'like', '%' . $this->search . '%')
                ->orWhereRaw("DATE_FORMAT(created_at, '%d %b %Y, %l:%i %p') like ?", [
                    '%' . $this->search . '%',
                ])
                ->orWhereHas('replies.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });

            if (strtolower(trim($this->search)) === 'no replies') {
                $q->orWhereDoesntHave('replies');
            }
        });
    }*/

    /**
     * Apply sorting to a query.
     *
     * @param $query
     * @return mixed
     */
    protected function applySorting($query): mixed
    {
        if ($this->sortBy && $this->sortDirection) {
            return $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query;
    }


}
