#!/bin/bash
# Start PHP built-in server for Render.com
# Render sets the PORT environment variable automatically
php -S 0.0.0.0:${PORT:-10000} -t /var/www/html router.php
