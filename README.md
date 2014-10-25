This is a simple script to setup a basic GeoIP appliance server.

The server uses a locally installed Redis server for query caching, as the
MaxMind querying system tends to be too slow for our needs. 

Installion:

1) Start with Turnkey Linux Core
2) Install Apache + PHP5
3) Install Redis Server
4) Clone this repository into /var/www/
5) Install MaxMind Database + PHP API into /var/www/_geoip/

And BAM, just like that you're done!

This service works with URLs in the given format:

http://yourserver/ipaddress



Examples:


Request:
http://yourserver/173.194.33.131

Returns:
{"country_code":"US","country_code3":"USA","country_name":"United States","region":"CA","city":"Mountain View","postal_code":"94043","latitude":37.4192,"longitude":-122.0574,"area_code":650,"dma_code":807,"metro_code":807,"continent_code":"NA"}



Request:
http://yourserver/2001:470:0:76::2

Returns:
{"country_code":"US","country_code3":"USA","country_name":"United States","region":"FL","city":"Lake Mary","postal_code":"32746","latitude":28.7578,"longitude":-81.3397,"area_code":407,"dma_code":534,"metro_code":534,"continent_code":"NA"}
