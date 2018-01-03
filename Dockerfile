FROM php:7.0.23-apache

RUN apt-get update && apt-get install -y libghc-postgresql-libpq-dev vim cron \
	&& docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ \ 
	&& docker-php-ext-install pgsql pdo_pgsql

# Development
COPY config/10kpizza-dev.conf /etc/apache2/sites-available/10kpizza.conf

# Copy over the crontab
COPY src/web-server/background/crontab.sh /etc/cron.d/background-cron
RUN chmod 0644 /etc/cron.d/background-cron
RUN cron

EXPOSE 80

VOLUME ["/var/log/apache2"]

RUN a2enmod rewrite
RUN a2ensite 10kpizza.conf
RUN a2dissite 000-default.conf

# Absolute garbage: https://github.com/sameersbn/docker-gitlab/issues/173
# More garbage: http://stackoverflow.com/questions/21926465/issues-running-cron-in-docker-on-different-hosts
# Causes this garbage:
RUN sed -i '/session    required     pam_loginuid.so/c\#session    required     pam_loginuid.so' /etc/pam.d/cron

# Copy the file to run on start to the home directory
COPY src/web-server/background/run.sh /root/run.sh

CMD ["/bin/bash", "/root/run.sh"]