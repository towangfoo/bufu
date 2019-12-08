FROM mysql:latest

ARG SERVICE_DIR="./mysql"
COPY ./shared/scripts/ /tmp/scripts/
RUN chmod -R 777 /tmp/scripts/

# set timezone
ARG TZ=UTC
RUN /tmp/scripts/set_timezone.sh ${TZ}

RUN /tmp/scripts/install_software.sh

# environment
ARG MYSQL_ROOT_PASSWORD=password
ARG MYSQL_USER=dev
ARG MYSQL_PASSWORD=dev
ARG MYSQL_DATABASE=dev
ENV MYSQL_ROOT_PASSWORD ${MYSQL_ROOT_PASSWORD}
ENV MYSQL_USER ${MYSQL_USER}
ENV MYSQL_PASSWORD ${MYSQL_PASSWORD}
ENV MYSQL_DATABASE ${MYSQL_DATABASE}

# config
COPY ${SERVICE_DIR}/mysql.cnf /etc/mysql/conf.d/

# cleanup
RUN /tmp/scripts/cleanup.sh

CMD ["mysqld"]

EXPOSE 306
