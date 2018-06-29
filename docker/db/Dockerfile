FROM mysql:5.5

ADD ./app/sql /docker-entrypoint-initdb.d
RUN chmod -R +rx /docker-entrypoint-initdb.d \
    && chown -R mysql:mysql /docker-entrypoint-initdb.d
