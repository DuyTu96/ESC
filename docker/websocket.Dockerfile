FROM alpine:latest

RUN apk --no-cache add python make g++ gcc nodejs npm bash
RUN npm install -g n
RUN n 6.14.3

WORKDIR /usr/share/nginx/html
CMD ["/usr/share/nginx/html/docker/websocket.sh"]
