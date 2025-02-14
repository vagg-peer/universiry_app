# university_app
A Symfony-based application implementing clean architecture, MVC pattern, and domain-driven design principles.

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
After <b>symfony server:start</b> in cli will output the address that is serving the project. e.g. http://127.0.0.1:xxxxx. 
Run these commands will create the databse, and an admin user with the following credentials: <br>
email: admin@uni.gr <br>
password: Test123! <br>

The home page is a login page.


# The project
There are three user roles: Admin, Teacher, Student

<h5>Users Capabilities</h5>
Admins have access to the following pages
<ul>
    <li>/admin</li>
    <li>/admin/teacher, /admin/teacher/new, /admin/teacher/edit</li>
    <li>/admin/lesson, /admin/lesson/new, /admin/lesson/edit</li>
    <li>/lesson/student, /admin/student/new, /admin/student/edit</li>
</ul>

Admins can make all CRUD operations for teachers, students, lessons and grades. Grades cannot be editted or deleted

Teachers have access to the following page: /teacher. Teacher dashboard shows the lessons that are asigned to teacher.

Students have access to the following page: /student. Student dashboard shows the lessons that has grades.


