# PHP Sitemap Crawler

This is a simple PHP script that crawls a website, shows all found URLs live in the browser, and generates a `sitemap.xml` file.

## Features
- Crawl all internal links of a website
- Show crawling progress in real-time
- Automatically create `sitemap.xml`
- Simple interface with URL input + Start button

## Requirements
- PHP 7.4 or newer  
- Web server (Apache, Nginx or PHP built-in server)

## Usage
1. Place the `index.php` file on your server or local environment.
2. Start PHP built-in server (if needed):
   ```bash
   php -S localhost:8000
http://localhost:8000
Enter the site URL in the input box and click Start Crawling.

The script will:

Show URLs as it crawls

Generate sitemap.xml in the same folder

Example
If you enter https://example.com, the crawler will scan all pages under example.com and create a sitemap.xml file with the results.

License
MIT License

