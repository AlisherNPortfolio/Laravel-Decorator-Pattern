# Decorator Pattern

Decorator pattern-i biror klasdan olingan biror obyektga qo'shimcha funksionallik qo'shishda ishlatiladi. Bunda shu klasdan olingan boshqa obyektlarga ta'sir ko'rsatmaydi.

### Masala

Faraz qilaylik, `Post` modelimiz bor bo'lsin:

```bash
class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status', 'content', 'image', 'published_at'];

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', 'NOW()');
    }
}
```

`PostController` kontrollerimizda esa quyidagicha `index` metodi mavjud:

```bash
class PostController extends Controller
{
    public function index()
    {
        return Post::published()->get();
    }
}

```

Post-larni keshlash uchun va har safar so'rov kelganda ma'lumotlar bazasiga murojaat qilmaslik uchun quyidagicha kod yozamiz:

```bash
class PostController extends Controller
{
    public function index()
    {
        $minutes = 1440; // 1 kun
        $posts = Cache::remember('posts', $minutes, function () {
            return Post::published()->get();
        });

        return $posts;
    }
}
```

Endi, olinadigan post-lar 1 kunga keshlanadigan bo'ldi. Lekin, kodga qaraydigan bo'lsak, kontrollerga keragidan ko'p ma'lumotni berganmiz: qancha vaqtga keshlab turish, keshni ishga tushirishni ham o'zi bajaryapti.

Yana, aynan shu narsani boshqa modellar, masalan, Tags, Categories va Archives uchun ham HomeController kontrollerida qilsak kod hajmi kattalashib, uni o'qish va boshqarish qiyinlashib ketib qoladi.

### Repository Pattern

Ko'pgina hollarda, repository pattern-i decorator pattern-iga bog'lanadi.

Birinchi bo'lib, posts modeli uchun `Repository pattern`-ini qo'llaymiz. Buning uchun `app/Repositories/Contracts` papkasida `PostsRepositoryInterface` interfeysini ochamiz:

```bash
interface PostsRepositoryInterface
{
    public function get();

    public function find(int $id);
}
```

`app/Repositories` papkasida esa `PostsRepository` klasini ochamiz:

```bash
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
```

Repository-ning interfeysini klasiga bog'laymiz. Buning uchun, avval, RepositoryServiceProvider yaratamiz: `php artisan make:provider RepositoryServiceProvider`

So'ng, unda interfeys va klasni bog'laymiz:

```bash
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PostsRepositoryInterface::class, PostsRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
```

Endi, `PostsController`-ga o'zgarishlarni qo'llaymiz:

```bash
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
```

> Yuqorida repository-ni kontrollerda service orqali ishlatgan yaxshi. Lekin kod qisqaroq bo'lishi uchun service qo'llanmagan.

Repository-ni ishlatganimizdan so'ng, kontrollerimiz ancha toza bo'ldi.

### Decorator orqali keshlashni ishlatish

Boshida aytganimizday, decorator pattern-i biror klasning bitta obyektiga, boshqa obyektlariga ta'sir ko'rsatmasadan turib qo'shimcha imkoniyat qo'shish hisoblanadi.

Hozir ko'rayotgan misolimizda keshlash qo'shimcha funksionallik bo'lib hisoblanadi.

`app/Repositories` papkasida `PostsCacheRepository` klasini ochamiz:

```bash
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
```

Bu klasda, Kesh obyekti va PostsRepository obyektini qabul qilib, keyin PostsRepository  obyektiga keshlash imkoniyatini qo'shish uchun Decorator klasni qo'llayapmiz.

Oxirgi qiladigan narsamiz, RepositoryServiceProvider-da PostsRepository o'rniga PostsCacheRepository-ni interfeysga bog'laymiz:

```bash
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PostsRepositoryInterface::class, PostsCacheRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
```









https://dev.to/ahmedash95/design-patterns-in-php-decorator-with-laravel-5hk6
