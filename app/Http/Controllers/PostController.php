<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\PostsRepositoryInterface;

class PostController extends Controller
{
    protected $repository;

    public function __construct(PostsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->get();
    }
}
