# university_app

# Prerequisite
PHP >= 8.2
Mysql >= 8
Symfony CLI
Composer >= 2.6.5

# Run the project
<ul>
    <li>Download project</li>
    <li>Add to this . env with the database info <b>DATABASE_URL="mysql://db_user:db_password@db_host/university_app?serverVersion=8.0.32&charset=utf8mb4"</b></li>
    <li>Use terminal to navigate to project's folder and run the following commands</li>
</ul>

```bash
$ composer install
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate
$ symfony server:start
```
After <b>symfony server:start</b> in cli will output the address that is serving the project. e.g. http://127.0.0.1:xxxxx
Run these commands will create the databse, and an admin user with the following credentials: <br>
email: admin@uni.gr <br>
password: Test123! <br>

