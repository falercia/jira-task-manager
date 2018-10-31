# Install Debian 9

## Install Requirements
- Apache
- PHP 7.2

```
apt-get install -y \
  apache2 \
  libapache2-mod-php7.2 \
  php7.2 \
  php7.2-cli \
  php7.2-common \
  php7.2-curl \
  php7.2-dev \
  php7.2-fpm \
  php7.2-gd \
  php7.2-imap \
  php7.2-json \
  php7.2-mbstring \
  php7.2-mysql \
  php7.2-opcache \
  php7.2-readline \
  php7.2-sqlite3 \
  php7.2-xml
```

## Enable Apache PHP Module
```
a2enmod php7.2
```

## Install Composer
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

# Running
```
git clone https://github.com/falercia/jira-task-manager.git

cd jira-task-manager

composer install
```
