#!/bin/sh

cd /usr/share/nginx/html
npm install
echo "Start up WebSocket server."
node nodejs/server.js
