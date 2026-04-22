Dockerビルド
・git clone
・docker compose up -d --build

Laravel環境構築
・docker compose exec php bash
・composer install
・cp .env.example .env 環境変数変更
・php artisan key:generate
・php artisan migrate
・php artisan db:seed

開発環境
・ユーザー登録：http://localhost:8027/register

・管理者ログイン：http://localhost:8027/admin/login

・phpMyAdmin：http://localhost:8080/

使用技術
・PHP 8.1.34
・Laravel 8.83.3
・MySQL 8.0.26
・nginx 1.21.1
