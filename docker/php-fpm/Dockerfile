FROM php:8.3-fpm

# Install necessary pacakges
RUN apt-get update && \
    apt-get install \
    libzip-dev \
    wget \
    git \
    unzip \
    -y --no-install-recommends
    

# Install php extensions
RUN docker-php-ext-install zip pdo_mysql

RUN apt-get update && \
    apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install gd

#Copy php.ini
COPY ./php.ini /usr/local/etc/php/



# # Copy composer installable
# COPY ./install-composer.sh ./

# # Cleanup packages and install composer
# RUN apt-get purge -y g++ \
#     && apt-get autoremove -y \
#     && rm -r /var/lib/apt/lists/* \
#     && rm -rf /tmp/* \
#     && sh ./install-composer.sh \
#     && rm ./install-composer.sh


# # COPY and run odbc installation script
# COPY ./install-odbc.sh ./
# RUN chmod +x install-odbc.sh
# RUN sh ./install-odbc.sh

#install redis
RUN wget http://pecl.php.net/get/redis-6.0.2.tgz
RUN pear install redis-6.0.2.tgz

# install odbc sql server driver
RUN apt-get update && apt-get install -y sudo
RUN apt-get install gnupg -y
RUN curl https://packages.microsoft.com/keys/microsoft.asc | sudo tee /etc/apt/trusted.gpg.d/microsoft.asc
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | sudo gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg
RUN curl https://packages.microsoft.com/config/debian/12/prod.list | sudo tee /etc/apt/sources.list.d/mssql-release.list
RUN sudo apt-get update
RUN sudo ACCEPT_EULA=Y apt-get install -y msodbcsql18
# optional: for bcp and sqlcmd
RUN sudo ACCEPT_EULA=Y apt-get install -y mssql-tools18



# enable sqlsrv

RUN apt-get install unixodbc-dev -y
RUN wget http://pecl.php.net/get/pdo_sqlsrv-5.12.0.tgz
RUN pear install pdo_sqlsrv-5.12.0.tgz
