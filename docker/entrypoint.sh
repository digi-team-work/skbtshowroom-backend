#!/bin/bash

# terminate on errors
set -e

# Check if volume is empty !!! work-only-docker-compose
if [ ! "$(ls -A "/var/www/wp-content/" 2>/dev/null)" ]; then
    echo 'Setting up wp-content volume'
    cp -r /usr/src/wordpress/wp-content /var/www/
    chown -R nobody.nobody /var/www
    curl -f https://api.wordpress.org/secret-key/1.1/salt/ >> /usr/src/wordpress/wp-secrets.php
fi
exec "$@"