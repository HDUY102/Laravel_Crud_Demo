<!-- Generic Database Query Handling Class -->
<?php

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = app()->make($this->getModelClass());
    }

    abstract protected function getModelClass(): string;

    public function paginate(int $perPage = 10)
    {
        return $this->model->latest()->paginate($perPage);
    }

    public function find(mixed $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function update(mixed $id, array $attributes): bool
    {
        $record = $this->find($id);
        return $record ? $record->update($attributes) : false;
    }

    public function delete(mixed $id): bool
    {
        $record = $this->find($id);
        return $record ? $record->delete() : false;
    }
}