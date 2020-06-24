<?php

declare(strict_types=1);

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Find.
     *
     * @param int $id
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($id);

    /**
     * Get all.
     *
     * @return Collection
     */
    public function all();

    /**
     * Check if model id is exist.
     *
     * @param int $id
     */
    public function existValidate($id): void;

    /**
     * Store.
     *
     * @param array $data
     *
     * @return
     */
    public function store($data);

    /**
     * Show.
     *
     * @param int $id
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function show($id);

    /**
     * Update.
     *
     * @param int   $id
     * @param array $data
     *
     * @return Model
     */
    public function update($id, $data);

    /**
     * Delete.
     *
     * @param array|Collection|int $ids
     *
     * @return int
     */
    public function destroy($ids);

    /**
     * Check exist.
     *
     * @param int $id
     *
     * @return bool
     */
    public function exist($id);

    /**
     * Store file.
     *
     * @param string $name
     * @param string $path
     *
     * @return string
     */
    public function storeFile($name, $path);
}
