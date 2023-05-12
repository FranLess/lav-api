<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface BaseRepository
{
    public function store(array $attributes);
    public function update(Model $model, array $attributes);
    public function delete(Model $model);
}
