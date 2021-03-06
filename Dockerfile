FROM centos:centos7
MAINTAINER AIZAWA Hina <hina@bouhime.com>

ADD docker/nginx/nginx.repo /etc/yum.repos.d/
ADD docker/rpm-gpg/ /etc/pki/rpm-gpg/

RUN rpm --import \
        /etc/pki/rpm-gpg/RPM-GPG-KEY-CentOS-7 \
        /etc/pki/rpm-gpg/RPM-GPG-KEY-EPEL-7 \
        /etc/pki/rpm-gpg/RPM-GPG-KEY-remi \
            && \
    yum update -y && \
    yum install -y \
        curl nginx scl-utils \
        http://ftp.tsukuba.wide.ad.jp/Linux/fedora/epel/7/x86_64/e/epel-release-7-5.noarch.rpm \
        http://rpms.famillecollet.com/enterprise/7/safe/x86_64/remi-release-7.1-3.el7.remi.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/git19/epel-7-x86_64/download/rhscl-git19-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/nodejs010/epel-7-x86_64/download/rhscl-nodejs010-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/rh-postgresql94/epel-7-x86_64/download/rhscl-rh-postgresql94-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/v8314/epel-7-x86_64/download/rhscl-v8314-epel-7-x86_64.noarch.rpm \
            && \
    yum install -y \
        ImageMagick \
        git19-git \
        jpegoptim \
        nodejs010-npm \
        patch \
        php70-php-cli \
        php70-php-fpm \
        php70-php-gd \
        php70-php-intl \
        php70-php-mbstring \
        php70-php-mcrypt \
        php70-php-opcache \
        php70-php-pdo \
        php70-php-pecl-jsonc \
        php70-php-pecl-msgpack \
        php70-php-pecl-zip \
        php70-php-pgsql \
        php70-php-runtime \
        php70-php-xml \
        pngcrush \
        rh-postgresql94-postgresql \
        rh-postgresql94-postgresql-server \
        supervisor \
            && \
    yum clean all && \
    ln -s /var/opt/rh/rh-postgresql94/lib/pgsql /var/lib/pgsql/rh-postgresql94 && \
    useradd statink && \
    chmod 701 /home/statink

ADD docker/env/scl-env.sh /etc/profile.d/
ADD docker/supervisor/* /etc/supervisord.d/
ADD . /home/statink/stat.ink
RUN chown -R statink:statink /home/statink/stat.ink

USER statink
RUN cd ~statink/stat.ink && bash -c 'source /etc/profile.d/scl-env.sh && make clean && make init'

USER postgres
RUN scl enable rh-postgresql94 'initdb --pgdata=/var/opt/rh/rh-postgresql94/lib/pgsql/data --encoding=UNICODE --locale=en_US.UTF8'
ADD docker/database/pg_hba.conf /var/opt/rh/rh-postgresql94/lib/pgsql/data/pg_hba.conf
ADD docker/database/password.php /var/opt/rh/rh-postgresql94/lib/pgsql/
RUN scl enable rh-postgresql94 php70 ' \
        /opt/rh/rh-postgresql94/root/usr/libexec/postgresql-ctl start -D /var/opt/rh/rh-postgresql94/lib/pgsql/data -s -w && \
        createuser -DRS statink && \
        createdb -E UNICODE -O statink -T template0 statink && \
        php /var/opt/rh/rh-postgresql94/lib/pgsql/password.php && \
        /opt/rh/rh-postgresql94/root/usr/libexec/postgresql-ctl stop -D /var/opt/rh/rh-postgresql94/lib/pgsql/data -s -m fast'

USER root
RUN cd ~statink/stat.ink && \
    bash -c ' \
        source /etc/profile.d/scl-env.sh && \
        su postgres -c "/opt/rh/rh-postgresql94/root/usr/libexec/postgresql-ctl start -D /var/opt/rh/rh-postgresql94/lib/pgsql/data -s -w" && \
        su statink  -c "make" && \
        su postgres -c "/opt/rh/rh-postgresql94/root/usr/libexec/postgresql-ctl stop -D /var/opt/rh/rh-postgresql94/lib/pgsql/data -s -m fast"'

ADD docker/php/php-config.diff /tmp/
RUN patch -p1 -d /etc/opt/remi/php70 < /tmp/php-config.diff && rm /tmp/php-config.diff

ADD docker/nginx/default.conf /etc/nginx/conf.d/

CMD /usr/bin/supervisord
EXPOSE 80
