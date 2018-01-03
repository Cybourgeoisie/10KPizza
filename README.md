# 10KPizza

This repository is the full package (front-end and back-end) for the [10K Pizza](https://www.10k.pizza) cryptocurrency portfolio. The front-end package can be run without a backend, and [the repo can be found here](https://github.com/Cybourgeoisie/cryptofolio).

## Download

To download and install 10K Pizza locally, you'll need to clone with the "recursive" flag since this repository includes several other repositories:

```git clone --recursive https://github.com/Cybourgeoisie/10kpizza.git```

## Requirements

The 10K Pizza backend requires [Docker](https://www.docker.com) and Docker Compose. Using Docker, all of the required packages will be installed within individual containers. Download and install the latest version for your environment.

## Setup

10K Pizza uses Docker for development and production. To run the backend for either, you'll need to set some environment variables for the program to find the database configuration and determine environment-specific settings.

For development, it should suffice to use the following environment variables (stored in .bashrc on Linux or .bash_profile on Mac OSX). Add these to your .bashrc or .bash_profile script:

```
export TENKPIZZA_DB_HOST="database"
export TENKPIZZA_DB_NAME="10kpizza"
export TENKPIZZA_DB_USER="10kpizza_admin"
export TENKPIZZA_DB_PASS="10kpizza_admin_pass"
export TENKPIZZA_PASSWORD_SALT="10kpizza_password_salt"
```

## Running the site using docker

### (1) Build the Docker images

In the root directory, run the following:

```docker build -t 10kpizza-dev .```

In the /sql/ directory, run the following:

```docker build -t 10kpizza-db-dev .```

### (2) Set up the data persistence container for the database

Running docker images do not persist data. In order for the database to persist, we need to run a second copy of the database, in which we use it solely to hold the data. This way, we can turn off the computer and return to the same data later:

```docker run -i --name 10kpizza-db-data 10kpizza-db-dev /bin/echo "PostgreSQL data container"```

### (3) Use docker-compose to run the backend

From the root directory, start the docker images using docker-compose:

```
docker-compose up -d
docker-compose down
```

### (4) FIRST-TIME ONLY: Install the PHP packages using PHP Composer

If this is your first time running 10K Pizza, you'll need to install the required PHP packages using composer.

#### Install Composer:

Go to https://getcomposer.org/download/ and run the installation procedure. As of January 3rd, 2018, the instructions are to run the following on Linux or Mac:

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

#### Now install all packages:

Change the working directory to the root directory of the project, and run the composer package installation:

```
composer install
```

You can update the packages using

```
composer update
```

## Reporting Requests, Bugs & Issues

Feel free to add issues to the repository as necessary. If you want to get my attention quicker, you can ping me directly on Twitter: [@cybourgeoisie](https://twitter.com/cybourgeoisie) or [@10K_Pizza](https://twitter.com/10K_Pizza).

## Donations

Any crypto donations are welcome and greatly appreciated:

**Bitcoin:**
```1DvKL6bsvFbPLHzpfyi1gN14UTLeTMteP1```

**Bitcoin Cash:**
```1aXsChnzsjJZ5K5moNqeuxzniJ6p4atra```

**Ethereum:**
```0x0963f59FB09D899768Bcd2599529d2CAaC855d2c```

**Litecoin:**
```LTXtDghDffVX6jarwskRsgP63qYyuh8AC3```

**Monero:**
```41zJ821YXfp952H7XAFNpZimo1gsF8V8mhd8ioSYf6tvesq5Tjm2rW5F1qM4uCFFgATQztxXowd5Q9pR3hpQvgcz5Z4QMha```

**Decred:**
```DsiRCP7pvKF5RgEuUgT77cHVtBdwRwSWNxV```
