
composer install

npm run build

migrate ve seederları çalıştırmadan önce, .env dosyası içerisindeki database alanlarına kendi bilgilerinizi girmeniz gerekmektedir.

php artisan migrate

php artisan db:seed

php artisan serve
