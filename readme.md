[![Build Status](https://travis-ci.org/troccoli/lva.svg?branch=master)](https://travis-ci.org/troccoli/lva)
[![CircleCI](https://circleci.com/gh/troccoli/lva.svg?style=shield)](https://app.circleci.com/pipelines/github/troccoli/lva?branch=master)
![](https://github.com/troccoli/lva/workflows/lva/badge.svg)
[![StyleCI](https://github.styleci.io/repos/50941713/shield)](https://github.styleci.io/repos/50941713)
![GitHub](https://img.shields.io/github/license/troccoli/lva?color=blue)

## About the project

I had been involved with the London Volleyball Association for many years, and when I was looking for a new project I
thought I helped them out with a new site.

__I am no longer associated with the LVA and this project is not in any shape or form associated with them.__

Although that was the initial motivation for the project, this is not what it is about anymore. I am now
using this project to showcase my skills and expertise with PHP and Laravel (and a bit of VueJS).

The code is not perfect, far from it. Firstly because you can always improve your code. But most importantly
because there are many ways of doing one thing and I try to show a few of them here. So, for example, I could have
use a package to build forms in blade component, but instead I choose to create Blade components and use them.

So, if you see something that looks different, or notice some inconsistencies, please remember that most likely
that is on purpose. I will try and document them as I go along.

- [Test Strategy and Data](./docs/tests.md)
- [API](./docs/api.md)
- [Views](./docs/views.md)
- [Roles and Permissions](./docs/roles-and-permissions.md)

## How to try it out

Since I use Docker for development, the following instructions are for Docker too, so before starting make sure you
have it installed.

After cloning or downloading this repository, I assume in `~/code/lva`, you will first of all need to spun-up the
containers:

```bash
cd ~/code/lva
docker-compose up -d
```
This will take a few minutes as Docker will have to download the images first. At the end you will have 4 containers:
- lva-web
- lva-mysql
- lva-mailhog
- lva-selenium

The lva-web container is where Apache and PHP are, and where your code is mounted on.

The lva-mysql container runs MySQL 5.7 and it has already a database called `lva`.

The lva-mailhog container runs Mailhog and it's used, if you don't know what Mailhog is, to capture all the
emails that the site sends and allow you to see them __without__ actually sending any emails to the outside world.

Finally, the lva-selenium container runs Selenium and it's used for the Laravel Dusk tests.

We now need to set-up the project. From now on all commands must run inside the lva-web container,
so let's go in there first: `docker exec -it lva-web /bin/bash`. You will be presented with a line similar
to `root@7db38fd1779e:/var/www/vhosts/app#`. I will omit it for the rest of the instructions.

So, inside the lva-web container run the following commands:
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
```

You're almost ready to go. If you want to add some dummy data to the database the run the `php artisan db:seed` command.

## The site

The site is available at `http://localhost:8081`.

If you want to connect to the database from your local machine, you can use the URL `localhost` and port `13306`. The
username is `root` and the password is `123`. Note that the actual database is within your project, at
`~/code/lva/.docker/database`.

If you want to check emails sent, you can open `http://localhost:18025` in your browser and use Mailhog.

## Testing

I use two tools for testing: PhpUnit and Laravel Dusk. But before being able to run the tests you need to create
the testing database. This is a SQLite database, which means that you can simply run the following, from inside the
lva-web container: `touch database/database.sqlite`.

Both PhpUnit and Laravel Dusk must be run inside the lva-web container. To run PhpUnit use the command
`docker-compose exec lva-web ./vendor/bin/phpunit`, and to run Laravel Dusk use the command
`docker-compose exec lva-web php artisan dusk`.

## Contributing

If you want to contribute please fork the repository and submit a Pull Request.

If you find a bug please submit an issue.

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Ideas

1. To set admin's preferences I can use the Laravel model settings package https://github.com/glorand/laravel-model-settings 
1. Use HATEOAS for the API https://github.com/gdebrauwer/laravel-hateoas?ref=laravelnews
