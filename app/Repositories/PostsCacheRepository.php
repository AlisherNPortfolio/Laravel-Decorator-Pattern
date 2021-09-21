<?php

namespace App\Repositories;

use App\Repositories\Contracts\PostsRepositoryInterface;
use Illuminate\Cache\CacheManager;

class PostsCacheRepository implements PostsRepositoryInterface
{
    protected $repo;

    protected $cache;

    const TTL = 1440; // 1 kun

    public function __construct(CacheManager $cache, PostsCacheRepository $repo)
    {
        $this->cache = $cache;
        $this->repo = $repo;
    }

    public function get()
    {
        return $this->cache->remember('posts', self::TTL, function () {
            return $this->repo->get();
        });
    }

    public function find(int $id)
    {
        return $this->cache->remember('posts' . $id, self::TTL, function () use ($id) {
            return $this->repo->find($id);
        });
    }
}
