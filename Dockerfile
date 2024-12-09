FROM docker-reg.oss.net.bd/rajuk-php8.1:v2

WORKDIR /var/www/html/
RUN rm -rf *
RUN chmod -R 755 /var/www/html
COPY --chown=www-data:www-data . .

RUN composer dump-autoload --no-scripts
	
COPY default.conf /etc/apache2/sites-enabled/000-default.conf


CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
EXPOSE 80 443