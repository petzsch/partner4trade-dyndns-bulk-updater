# partner4trade-dyndns-bulk-updater

(full documentation will follow soon)

## Quickstart

1. create a hosting account for this project (important: don't enable SSL - the fritz!box ddns client doesn't speak ssl)
2. checkout the project in your webroot directory
3. password protect the webroot with .htaccess (usually possible from within the hosting control panel)
4. cp config.dist.php to config.php and edit with your partner4trade.de and .htaccess credentials
5. create an empty directory zones.d
6. on the CLI use php -f zone-downloader.php <zone.com> to download all of your DNS zones that you wish to modify by this script
7. edit the zone file(s) to include the placeholders ({ipv4} and {ipv6} and {ipv6prefix}) -> the first two are the WAN IPs of the fritz!box, the later is your ipv6 prefix. (also update the Ttl value to 180 seconds)
8. edit your fritz!box settings to include: username, domain, password and update URL: `dyn-hosting-domain.tld/ipv4.php?ipv4=<ipaddr>&domain=<domain> dyn-hosting-domain.tld/index.php?ipv6=<ip6addr>&ipv6prefix=<ip6lanprefix>&domain=<domain>` (2 URLs seperated by a Wwitespace)
