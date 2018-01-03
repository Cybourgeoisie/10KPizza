FROM postgres:9.6.5

COPY setup /docker-entrypoint-initdb.d/

VOLUME  ["/etc/postgresql", "/var/log/postgresql", "/var/lib/postgresql"]

# Development
ENV POSTGRES_USER 10kpizza_admin
ENV POSTGRES_PASSWORD 10kpizza_admin_pass
ENV POSTGRES_DB 10kpizza

CMD ["postgres"]