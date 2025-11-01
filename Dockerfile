FROM php:8.2-cli
WORKDIR /app
COPY . /app
RUN echo "$(date -u +%FT%TZ)" > /app/build-id.txt
EXPOSE 10000
CMD ["bash","-lc","/app/start.sh"]
