<?php

namespace App\Actions\Company;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AllCompanies
{
    public function handle(array $attributes = [], array $relations = [], array $filters = [], array $hidden = [], array $appends = [], string $orderBy = 'created_at', string $orderDirection = 'asc', bool $withoutPagination = false, int|null $limit = null): Collection|LengthAwarePaginator
    {
        $data = Company::query()
            ->when($attributes ?? null, function ($query) use ($attributes) {
                $query->select($attributes);
            })
            ->with($relations)
            ->filter($filters)
            ->orderBy($orderBy, $orderDirection);

        if ($limit) {
            $data->limit($limit);
        }

        if (!$withoutPagination) {
            return $data
                ->paginate(10)
                ->withQueryString()
                ->through(fn($object) => $object->makeHidden($hidden)->setAppends($appends));
        }

        return $data->get()->each(fn($object) => $object->makeHidden($hidden)->setAppends($appends));
    }
}
