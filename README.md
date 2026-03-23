# laravel-artd
laravel后台

```
1、导入artd.sql

2、进入 backend，运行 composer install

3、运行 php artisan key:generate

3、复制一份 .env.example 为 .env 修改其中 mysql 和 redis 配置

4、进入 frontend，修改 .env.development 和 .env.production

VITE_BASE_URL = xxx
VITE_API_URL = xxx

5、pnpm install

6、pnpm dev 或 pnpm build

```