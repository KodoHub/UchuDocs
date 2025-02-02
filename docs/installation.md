# Installation Guide

## Prerequisites

- PHP 8.1 or higher
- Composer
- Web Server (Apache, Nginx, etc.)

## Installation Steps

1. Clone the repository:

```bash
git clone https://github.com/kodohub/uchudocs.git
cd docs
```

2. Install dependencies:

```bash
composer install
```

3. Configure Web Server

### Apache Configuration

Create a `.htaccess` file in the root directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?page=$1 [L,QSA]
```

### Nginx Configuration

```nginx
location / {
    try_files $uri $uri/ /index.php?page=$request_uri;
}
```

4. Point your web server's document root to the directory used on upload

## Troubleshooting

- Ensure all dependencies are installed via Composer
- Check file permissions
- Verify PHP version compatibility
