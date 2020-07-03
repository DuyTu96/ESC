Laravel Base is based on Laravel 6.18

## About Laravel Base

Laravel Base is the customized Laravel Project which has prepared environments, components designed for the specific DB schemes, source code easy to be diverted.  
You can develop API and Web application instantly with Laravel Base.

Install tools below on your local PC before starting.

- [Git](https://git-scm.com/)
- [Docker](https://www.docker.com/)

We recommend [VS Code](https://code.visualstudio.com/) as source code editor.

## 1. How to setup developing environment

#### 1-1. VSCode

Plug-ins for PHP debug are recommended by ".vscode/extensions.json" when you open the project with VSCode.  
Install them if you use VSCode.

#### 1-2. Create .env file for Docker

```
$ cp .env.docker .env
```

#### 1-3. Put SQL files

Put .sql files under "initdb" directory.
```
./docker/initdb
```
Those .sql files are automatically loaded when docker is starting.

In .env file, set DB name.
```
DB_DATABASE=(DB name)
```

#### 1-4. Start up Docker

```
$ docker-compose up
```
When needed to rebuild.
```
$ docker-compose build --no-cache
```

#### 1-5. Laravel container

Connect to the Laravel container.  
Need to create vendor directory and node_modules directory.
```
$ docker-compose exec laravel sh
(laravel)$ composer install
(laravel)$ npm install
(laravel)$ php artisan key:generate
(laravel)$ php artisan config:clear
(laravel)$ exit
$ docker-compose restart
```
([Composer](https://github.com/composer/composer) and [npm](https://www.npmjs.com/) are pre-installed in the container.)

#### 1-6. MySQL container

Connect to the MySQL container on another console tab.
```
$ docker-compose exec mysql sh
(mysql)$ mysql -u root -h 127.0.0.1 -p
Enter password: secret
mysql> use (your DB name)
mysql> ※ Execute any SQL.
mysql> exit
(mysql)$ exit
```

#### 1-7. Docker commands for frequent use.

Start up containers.
```
$ docker-compose up
```
Stop containers.
```
$ docker-compose stop
```
Delete stopped containers.
```
$ docker-compose rm
```
Stop and delete containers.
```
$ docker-compose down
```

#### 1-8. Debug with xdebug

In ./docker/php.ini

```
xdebug.remote_host=192.168.88.250
```

Change "xdebug.remote_host" to your local IP.

Get your local IP:  
on Mac
```
$ ifconfig
```
on Windows
```
$ ipconfig
```

## 2. How to commit
Create a branch from develop branch following [A successful Git branching model](https://nvie.com/posts/a-successful-git-branching-model/).

Feature branch name rule is here.
```
feature/(your_name)_yyyymmdd_(issue_id)
```

Before you commit on Git, analyze and fix source code by [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).
```
$ php vendor/bin/php-cs-fixer fix
```

## 3. How to deploy to Ubuntu server with [Deployer](https://deployer.org/) and its [recipes](https://github.com/deployphp/recipes)

### 3-1. ToDo before initial deploy.

#### 3-1-1. Link to GitHub

Connect to Ubuntu server and generate public/private rsa key pair.
```
$ cd /home/ubuntu/.ssh
$ ssh-keygen -t rsa
```
Register id_rsa.pub (public key) to [GitHub page](https://github.com/settings/ssh).  
Confirm that you can access GitHub from Ubuntu.
```
$ ssh -T git@github.com
```

#### 3-1-2. Change permission
```
$ sudo chown ubuntu:ubuntu -R /usr/share/nginx/html
```

#### 3-1-4. Edit .env of each environment
Edit .env files below.
- .env.testing
- .env.staging
- .env.production

### 3-2. How to deploy
Deploy to an environment.
```
php vendor/bin/dep deploy [deploy to] -vvv --branch="[remote branch name]"
```
[deploy to] includes "testing", "staging" and "production".  
You can see output with "-vvv" when executing.

Deploy to staging env without "--branch" option: develop branch selected.  
Deploy to production env without "--branch" option: master branch selected.

When you want to restore deployment.
```
$ php vendor/bin/dep rollback [deploy to]
```

### 3-3. ToDo after initial deploy.

#### 3-3-1. Change document root of Nginx.
```
/usr/share/nginx/html
　↓
/usr/share/nginx/html/current
```

## 5. Log viewer
You can view server logs on browser by [Laravel log viewer](https://github.com/rap2hpoutre/laravel-log-viewer).

Open on browser.
```
http://(your domain)/logs
```
