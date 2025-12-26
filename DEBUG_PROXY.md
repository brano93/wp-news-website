# Debugging Proxy Image Downloads

## Step 1: Check Environment Variables in Container

On your Digital Ocean server, check if the environment variables are set in the WordPress container:

```bash
cd /opt/wp-news-website

# Check if environment variables are set
docker compose exec wordpress printenv | grep RSI_IMAGE_PROXY

# Or check directly
docker compose exec wordpress printenv RSI_IMAGE_PROXY_HOST
docker compose exec wordpress printenv RSI_IMAGE_PROXY_PORT
```

## Step 2: Check if Proxy Settings Are in .env File

```bash
cd /opt/wp-news-website

# Check .env file (if it exists)
cat .env | grep RSI_IMAGE_PROXY

# Or check docker-compose.yml
grep -A 2 RSI_IMAGE_PROXY docker-compose.yml
```

## Step 3: Set Environment Variables (if not set)

If the variables are not set, add them to your `.env` file:

```bash
cd /opt/wp-news-website
nano .env
```

Add these lines:
```
RSI_IMAGE_PROXY_HOST=127.0.0.1
RSI_IMAGE_PROXY_PORT=20003
```

Then restart:
```bash
docker compose down
docker compose up -d
```

## Step 4: Check WordPress Debug Logs

The plugin now logs debug information. Check the WordPress debug log:

```bash
cd /opt/wp-news-website

# Check debug log inside container
docker compose exec wordpress tail -100 /var/www/html/wp-content/debug.log | grep -i "rss smart importer"

# Or follow the log in real-time
docker compose exec wordpress tail -f /var/www/html/wp-content/debug.log | grep -i "rss"
```

## Step 5: Test Proxy from Inside Container

Test if the proxy works from inside the WordPress container:

```bash
cd /opt/wp-news-website

# Test curl from inside container
docker compose exec wordpress curl -v -x http://127.0.0.1:20003 https://cdn.nezavisne.com/2025/12/750x450/20251226084801_944115.webp -o /tmp/test-image.webp

# Check if file was downloaded
docker compose exec wordpress ls -lh /tmp/test-image.webp
```

## Step 6: Check PHP cURL Extension

Verify that cURL is available in PHP:

```bash
cd /opt/wp-news-website

# Check if cURL extension is loaded
docker compose exec wordpress php -m | grep curl

# Check cURL version
docker compose exec wordpress php -r "echo curl_version()['version'];"
```

## Step 7: Test with WordPress WP-CLI (if available)

If WP-CLI is available, you can test directly:

```bash
cd /opt/wp-news-website

# Check if WP-CLI is available
docker compose exec wordpress wp --info

# Check environment variables in WordPress
docker compose exec wordpress wp eval 'var_dump(getenv("RSI_IMAGE_PROXY_HOST"), getenv("RSI_IMAGE_PROXY_PORT"));'
```

## Step 8: Enable WordPress Debug Mode

Make sure WordPress debug logging is enabled. Check your `.env` file:

```bash
cd /opt/wp-news-website
cat .env | grep WP_DEBUG
```

Should show:
```
WP_DEBUG=1
```

Or check in `wp-config.php` that `WP_DEBUG_LOG` is set to true.

## Common Issues:

1. **Environment variables not passed to container**: Check docker-compose.yml has the variables defined
2. **Proxy not accessible from container**: Use `127.0.0.1` if proxy is on host, or container name if it's another container
3. **cURL not available**: The WordPress PHP image should have cURL, but verify it's installed
4. **Wrong proxy format**: Should be `127.0.0.1:20003` (host:port format)

## Expected Log Output:

When working correctly, you should see in the debug log:

```
[RSS Smart Importer] Image download attempt for: https://cdn.nezavisne.com/...
[RSS Smart Importer] Proxy host: 127.0.0.1
[RSS Smart Importer] Proxy port: 20003
[RSS Smart Importer] Using proxy: 127.0.0.1:20003
[RSS Smart Importer] Starting proxy download - URL: https://..., Proxy: 127.0.0.1:20003
[RSS Smart Importer] cURL result: SUCCESS
[RSS Smart Importer] HTTP code: 200
[RSS Smart Importer] Successfully downloaded image via proxy (size: X bytes)
```

