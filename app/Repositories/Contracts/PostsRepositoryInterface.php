<?php

namespace App\Repositories\Contracts;

interface PostsRepositoryInterface
{
    public function get();

    public function find(int $id);
}
