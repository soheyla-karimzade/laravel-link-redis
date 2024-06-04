<?php

namespace App\Repositories;

use App\Models\Link;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LinkRepository implements  LinkRepositoryInterface
{

    protected Link $model;
    protected  $userId;

    public function __construct(Link $link)
    {
        $this->model = $link;
        $this->userId = Auth::id();
    }

    public function all()
    {
        return Cache::remember("user:{$this->userId }:link:*", 1, function () {
            return  $this->model::all();
        });
    }

    public function find($id)
    {
        return Cache::remember("user:{$this->userId }:link:{$id}", 1, function() use ($id) {
            return $this->model->findOrFail($id);
        });
    }

    public function create(array $data)
    {
        $link = $this->model->create($data);
        $key = "user:{$data['user_id']}:link:{$link->id}";
        Cache::remember($key, 1, function () use ($link) {
            return $link;
        });
        return $link;
    }

    public function update($id, array $data)
    {
        $link = $this->model->findOrFail($id);
        $link->update($data);
        $key = "user:{$this->userId }:link:{$id}";

        Cache::forget($key);

       Cache::remember($key, 1, function () use ($link) {
            return $link;
        });

        return json_decode(Cache::get($key), true);
    }

    public function delete($id)
    {
        $this->model->destroy($id);
        Cache::forget('links.all');
        Cache::forget("user:{$this->userId }:link:{$id}");
    }

    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }
}
