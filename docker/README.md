# UchuDocs Docker Setup

This Docker configuration provides a complete setup for running UchuDocs in a containerized environment.

## Prerequisites

- Docker installed on your system
- Docker Compose installed
- Git (to clone the repository)

## Quick Start

1. **Clone your repository** (if not already done):
   ```bash
   git clone https://github.com/KodoHub/UchuDocs.git
   cd UchuDocs
   ```

2. **Create the Docker directory structure**:
   ```bash
   mkdir -p docker
   ```

3. **Copy the configuration files**:
   - Save the `Dockerfile` in your project root
   - Save the `docker-compose.yml` in your project root
   - Save the `.htaccess` file in your project root
   - Save the `.dockerignore` in your project root

4. **Build and run the container**:
   ```bash
   # Using Docker Compose (recommended)
   docker-compose up -d --build
   
   # Or using Docker directly
   docker build -t uchudocs .
   docker run -d -p 8080:80 --name uchudocs-app uchudocs
   ```

5. **Access your application**:
   Open your browser and navigate to: `http://localhost:8080`

## File Structure

After setup, your project should have this structure:
```
UchuDocs/
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── .htaccess
├── docs/
├── templates/
├── src/
├── composer.json
└── index.php
```

## Configuration Details

### Docker Image Features
- **Base**: PHP 8.1 with Apache
- **Extensions**: PDO MySQL, mbstring, GD, ZIP, and more
- **Composer**: Latest version included
- **Security**: Proper file permissions and security headers
- **Performance**: Compression and caching enabled

### Volumes
The Docker Compose setup mounts the following directories:
- `./docs` → `/var/www/html/docs` (your documentation files)
- `./templates` → `/var/www/html/templates` (view templates)
- `./src` → `/var/www/html/src` (core application logic)

This allows you to edit files on your host machine and see changes immediately.

## Development Workflow

1. **Start the container**:
   ```bash
   docker-compose up -d
   ```

2. **View logs**:
   ```bash
   docker-compose logs -f uchudocs
   ```

3. **Stop the container**:
   ```bash
   docker-compose down
   ```

4. **Rebuild after changes**:
   ```bash
   docker-compose up -d --build
   ```

## Database Support

If you need database support in the future, uncomment the MySQL service in the `docker-compose.yml` file. The configuration includes:
- MySQL 8.0
- Database: `uchudocs`
- User: `uchudocs`
- Password: `password`

## Customization

### Environment Variables
You can customize the setup by adding environment variables to the `docker-compose.yml`:

```yaml
environment:
  - PHP_MEMORY_LIMIT=256M
  - PHP_UPLOAD_MAX_FILESIZE=10M
  - PHP_POST_MAX_SIZE=10M
```

### Apache Configuration
Modify the `.htaccess` file to:
- Change rewrite rules
- Add custom security headers
- Configure caching policies
- Set up redirects

### PHP Configuration
Create a custom `php.ini` file and mount it:
```yaml
volumes:
  - ./docker/php.ini:/usr/local/etc/php/php.ini
```

## Troubleshooting

### Permission Issues
If you encounter permission issues:
```bash
sudo chown -R $USER:$USER docs templates src
chmod -R 755 docs templates src
```

### Container Won't Start
Check the logs:
```bash
docker-compose logs uchudocs
```

### Port Already in Use
Change the port mapping in `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Use port 8081 instead
```

## Production Deployment

For production deployment:

1. **Use environment-specific compose file**:
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

2. **Add SSL/TLS support**
3. **Configure proper logging**
4. **Set up monitoring**
5. **Use secrets for sensitive data**

## Commands Reference

```bash
# Build the image
docker-compose build

# Start services
docker-compose up -d

# View running containers
docker-compose ps

# Execute commands in container
docker-compose exec uchudocs bash

# View logs
docker-compose logs -f

# Stop services
docker-compose down

# Remove everything including volumes
docker-compose down -v
```

## Security Notes

- The configuration includes basic security headers
- Sensitive files are protected from direct access
- Use environment variables for sensitive configuration
- Consider using Docker secrets in production
- Regularly update the base PHP image for security patches
