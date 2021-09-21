<?php

namespace App\Repositories;

use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Models\Post;

class PostsRepository implements PostsRepositoryInterface
{
    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        return $this->model->published()->get();
    }

    public function find(int $id)
    {
        return $this->model->published()->find($id);
    }
}
