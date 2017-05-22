# Crablor

Simple website crawler.

:warning: This tool was made for demonstration purposes only.

## Usage

Install dependencies:

    composer install --ignore-platform-reqs

See usage:

    crablor.php --help
    
Run:

    crablor.php "https://the.site" --out sitemap.txt --sleepMS 1000
    
#### Run in Docker

The following method wraps the script with shell. It's necessary to make ctrl-c work in docker interactive mode, 
because php does not handle signals by default.

    docker run -i -t --rm --name crablor -v "$PWD":/app -w /app php:7.1-alpine /bin/sh -c '/app/crablor.php "https://the.site" --out sitemap.txt --sleepMS 1000'
   
If you do not care, then just ust:
    
    docker run -i -t --rm --name crablor -v "$PWD":/app -w /app php:7.1-alpine /app/crablor.php "https://the.site" --out sitemap.txt --sleepMS 1000

## License

[MIT](https://github.com/hypnoglow/crablor/blob/master/LICENSE.md).
