# WordPress News Website

WordPress site with RSS Smart Importer plugin for automated content aggregation.

## Features

- WordPress 6.x with PHP 8.2
- RSS Smart Importer - Custom plugin for RSS feed aggregation
- Docker containerization for easy deployment
- Multiple RSS feed support with scheduling
- Image downloading and optimization
- Duplicate detection and content updates

## Tech Stack

- **WordPress**: Latest version
- **PHP**: 8.2
- **Database**: MySQL 8.0
- **Containerization**: Docker & Docker Compose
- **Web Server**: Apache (via WordPress Docker image)

## Local Development Setup

### Prerequisites

- Docker Desktop or Docker Engine
- Docker Compose

### Quick Start

1. Clone the repository:
```bash
git clone git@github.com:brano93/wp-news-website.git
cd wp-news-website
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Update `.env` with your local configuration:
```bash
nano .env
```

4. Start the services:
```bash
docker compose up -d
```

5. Access the site:
- Frontend: http://localhost:8080
- Admin: http://localhost:8080/wp-admin

### Import Database (First Time)

If you have a database backup:

```bash
docker compose exec -T db mysql -u root -pYOUR_ROOT_PASSWORD wp < backup.sql
```

## RSS Smart Importer Plugin

Custom plugin for importing RSS feeds with:

- Multiple feed URL support
- Customizable scheduling (HH:MM intervals)
- Duplicate detection using GUID
- Image downloading and local storage
- Featured image assignment
- Category mapping (kebab-case → Title Case)
- Manual run capability

### Configuration

Configure via WordPress admin or environment variables:

- `RSI_API_HOST` - RSS API endpoint
- `RSI_API_BEARER_TOKEN` - API authentication token

## Docker Services

- **wordpress**: WordPress PHP 8.2 Apache container
- **db**: MySQL 8.0 database container

### Useful Commands

```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# View logs
docker compose logs -f

# Restart services
docker compose restart

# Access WordPress container
docker compose exec wordpress bash

# Access database
docker compose exec db mysql -u root -p wp
```

## Environment Variables

See `.env.example` for all available environment variables.

Key variables:
- `MYSQL_DATABASE` - Database name
- `MYSQL_USER` - Database user
- `MYSQL_PASSWORD` - Database password
- `WP_PORT` - WordPress port (default: 80)
- `WP_DEBUG` - Debug mode (0/1)
- `RSI_API_HOST` - RSS API endpoint
- `RSI_API_BEARER_TOKEN` - API token

## Deployment

See deployment documentation for Digital Ocean setup (not included in repo for security).

## Project Structure

```
.
├── docker-compose.yml    # Docker services configuration
├── wp-config.php         # WordPress configuration (reads from env vars)
├── wp-content/           # WordPress content
│   ├── plugins/          # Plugins directory
│   │   └── rss-smart-importer/  # Custom RSS importer plugin
│   ├── themes/           # Themes directory
│   └── uploads/          # Media uploads (excluded from git)
├── .env.example          # Environment variables template
└── README.md             # This file
```

## Notes

- `.env` file is excluded from git (contains sensitive data)
- Database backups are excluded from git
- Uploads directory is included (uncomment in .gitignore to exclude)
- Deployment scripts and docs are excluded from git

## License

[Your License Here]

## Author

Branimir Ilic

