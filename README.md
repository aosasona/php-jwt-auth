# PHP Starter

This is a PHP starter project. It allows you to set up a dockerized PHP project loaded with the bare minimum you need fast. It also includes a good routing package suitable for APIs and static sites; the `.htaccess` file has been written to match this.

## Requirements
- PHP 7.1+
- Docker and Docker Compose

## Includes
- [Apache2](https://www.apache.org/)
- [PHP](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [PHPMyAdmin](https://www.phpmyadmin.net/)
- [MySQL](https://www.mysql.com/)
- [PHPRouter](https://phprouter.herokuapp.com/)

## Setup
Run the following command to run it in detached mode:
```bash
$ docker-compose up -d
```

To force-rebuild the images, use the included `Setup.sh` script.

## Usage
To create your own migrations, add it to the `cli.php` file and use the following command to perform the migration (it is also run while your container is building):
```bash
$ php cli.php migrate:fresh
```

> **Note**: This will ONLY work in the CLI.

You can also edit the `api.conf` file to change your Apache configuration. The installed router depends on your `.htaccess` file, be careful with that.