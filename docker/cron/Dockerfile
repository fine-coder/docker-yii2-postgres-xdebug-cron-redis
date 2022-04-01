ARG COMPOSE_PROJECT_NAME
FROM ${COMPOSE_PROJECT_NAME}_php

RUN apt-get update && \
    apt-get -y install \
            cron \
        --no-install-recommends && \
        apt-get clean && \
        rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY crontab /etc/cron.d/cron
RUN chmod 0644 /etc/cron.d/cron
RUN crontab /etc/cron.d/cron

RUN touch /var/log/cron.log

CMD cron && tail -f /var/log/cron.log