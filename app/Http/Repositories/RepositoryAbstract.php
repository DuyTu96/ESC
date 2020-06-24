<?php

declare(strict_types=1);

namespace App\Repositories;

use Carbon;
use Storage;
use Validator;

abstract class RepositoryAbstract implements RepositoryInterface
{
    /**
     * @var string Model name
     */
    protected $model;

    /**
     * @var string Table name
     */
    protected $table;

    /**
     * @var array Validation rules for store
     */
    protected $storeRules;

    /**
     * @var array Validation rules for update
     */
    protected $updateRules;

    /**
     * @var array Column names
     */
    protected $columnNames;

    protected $with;

    protected $columns = ['*'];

    /**
     * Construct.
     */
    public function __construct()
    {
    }

    /**
     * Find.
     *
     * @param int $id
     *
     * @return array
     */
    public function find($id)
    {
        $model = $this->model::with($this->with)->find($id, $this->columns);

        return empty($model) ? [] : $model;
    }

    public function with(array $with = [''])
    {
        if (is_array($with) === false) {
            throw new RepositoryException('');
        }

        $this->with = $with;

        return $this;
    }

    /**
     * @param array $columns
     * @throws RepositoryException
     * @return $this
     */
    public function columns(array $columns = ['*'])
    {
        if (is_array($columns) === false) {
            throw new RepositoryException('');
        }
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get all.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Store validation.
     *
     * @param array $data
     *
     * @return array Error messages
     */
    public function storeValidate($data)
    {
        $validator = Validator::make($data, $this->storeRules, [], $this->columnNames);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return [];
    }

    /**
     * Check if model id is exist.
     *
     * @param int $id
     */
    public function existValidate($id): void
    {
        if (!$this->exist($id)) {
            abort(404, __(':name not found!', ['name' => __($this->modelName)]));
        }
    }

    /**
     * Store.
     *
     * @param array $data
     */
    public function store($data)
    {
        return $this->model::create($data);
    }

    /**
     * Show.
     *
     * @param int $id
     *
     * @return array
     */
    public function show($id)
    {
        $model = $this->model::find($id, $this->columns);

        if (empty($model)) {
            return [];
        }

        return $model->toArray();
    }

    /**
     * Update validation.
     *
     * @param int   $id
     * @param array $data
     *
     * @return array Error messages
     */
    public function updateValidate($id, $data)
    {
        $validator = Validator::make($data, $this->updateRules, [], $this->columnNames);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return [];
    }

    /**
     * Update.
     *
     * @param array $id
     * @param array $data
     */
    public function update($id, $data): void
    {
        $this->model::find($id)->update($data);
    }

    /**
     * Destroy.
     *
     * @param array|Collection|int $ids
     */
    public function destroy($ids): void
    {
        $this->model::destroy($ids);
    }

    /**
     * Check exist.
     *
     * @param int $id
     *
     * @return bool
     */
    public function exist($id)
    {
        return !empty($this->find($id));
    }

    /**
     * Upload files.
     *
     * @param array|object $files
     * @param string       $dir
     *
     * @return
     */
    public function upload($files, $dir)
    {
        $urls = [];
        $error = null;

        try {
            if (is_array($files)) {
                foreach ($files as $file) {
                    $urls[] = $this->putFile($file, $dir);
                }
            } else {
                $urls[] = $this->putFile($files, $dir);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();

            // delete all saved files
            Storage::delete($urls);

            // reset urls var
            $urls = [];
        }

        return [
            'urls' => $urls,
            'error' => $error,
        ];
    }

    /**
     * Store file.
     *
     * @param string $file
     * @param string $dir
     *
     * @return string
     */
    public function storeFile($file, $dir)
    {
        $arrData = [
            'status' => 200,
        ];
        $time = new Carbon();
        $now = $time->format('Ymdhms');

        $nameonly = preg_replace('/\..+$/', '', $file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();
        $fileName = $now . '_' . uniqid() . '.' . $extension;
        $filePath = $dir . $fileName;

        if (Storage::put($filePath, file_get_contents($file))) {
            $arrData['image_url'] = Storage::url($filePath);
            $arrData['storage_path'] = $filePath;

            return $arrData;
        }

        $arrData['status'] = 400;
        $arrData['error'] = 'パスは存在しません';

        return $arrData;
    }

    /**
     * Put file to server.
     *
     * @param object $file
     * @param string $dir
     *
     * @return string File path
     */
    private function putFile($file, $dir)
    {
        $time = new Carbon();
        $now = $time->format('Ymdhms');

        $extension = $file->getClientOriginalExtension();
        $fileName = $now . '_' . uniqid() . '.' . $extension;
        $filePath = $dir . '/' . $fileName;
        Storage::put($filePath, file_get_contents($file));

        return Storage::url($filePath);
    }
}
