FROM fauria/lamp:latest
LABEL maintainer="Kyle Polich"

RUN mkdir -p /app
WORKDIR /app
COPY . /app

## Expose used ports
EXPOSE 80

## Run
CMD ["/bin/sh"]
