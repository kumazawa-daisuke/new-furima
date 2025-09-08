# フリマアプリ
## 環境構築
### Dockerビルド
1. git clone git@github.com:kumazawa-daisuke/new-furima.git
2. DockerDesktopアプリを立ち上げる
3. docker-compose up -d --build
### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. 「.env.example」ファイルを「.env」ファイルに命名を変更。または新しく.envファイルを作成
4. .envに以下の環境変数を追加

DB_CONNECTION=mysql  
DB_HOST=mysql  
DB_PORT=3306  
DB_DATABASE=laravel_db  
DB_USERNAME=laravel_user  
DB_PASSWORD=laravel_pass  

MAIL_MAILER=smtp  
MAIL_HOST=mailhog  
MAIL_PORT=1025  
MAIL_USERNAME=null  
MAIL_PASSWORD=null  
MAIL_ENCRYPTION=null  
MAIL_FROM_ADDRESS=example@example.com  
MAIL_FROM_NAME="${APP_NAME}"  

5.アプリケーションキーの作成  
php artisan key:generate  
6.マイグレーションの実行  
php artisan migrate  
7.シーディングの実行  
php artisan db:seed  
8.シンボリックリンクの作成  
php artisan storage:link  
9.Stripeキーの設定  
※stripeのユーザー登録は済ませておいてください  
.envファイルに公開可能キーとシークレットキーを追加  
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxxxx  
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxxxx  

## ダミーユーザー用ログインアドレス・パスワード  
出品者1  
メールアドレス:test1@example.com  
出品者2  
メールアドレス:test2@example.com  
購入者  
メールアドレス:test3@example.com  
パスワード(共通):password  

## 備考  
・商品購入時はコンビニ決済では購入を完了できません   
・クレジットカード決済でカード番号を424242424242,期限を先の年月とすると購入を完了できます  
・購入完了後にサンクスページを追加しています  
・ログインしないで商品一覧ページにアクセスした際にマイリストが表示されないようにしています  


## 使用技術(実行環境)
・PHP8.0.30
・Laravel8.83.8  
・MySQL8.0.26  
・MailHog（開発用メールサーバ）  

## ER図
<img width="771" height="1051" alt="E-R - new-furima" src="https://github.com/user-attachments/assets/49c96fab-50aa-4b18-957e-40e5bc6f092a" />


## URL
・開発環境：http://localhost/  
・phpMyAdmin:http://localhost:8080/  
・mailhog:http://localhost:8025/
