php-cachedf
===========

A PHP library to speed-up your site

##How this works?
when you call a function cached, we will save the return associating with: 
* the arguments values
* the name of function
* the file of function
* others variables (user-defined)

when you call again this function (even by other php, client, day) we get the return cached. You always can invalidate a data (or group of data) by calling "flush_cache" with the appropriate arguments.

##when NOT to use
* DON'T use: in functions which makes data updates (databases, variables, etc);
* DON'T use: in functions which write data to stdout ("printf", "echo", ...) or send data to any resource (like files);
* DON'T use (or use with care): in functions that return sensible information (like user profile);
* DON'T use: in fast functions because this library has overhead so, if you do your site will slow-down;

ALWAYS keep in mind that cachedf will cache the "return" of function

##How-to use
* include "cachedf.php"
* at first line of your function include this: 
```php
if (cachedf()) return cachedf_val();
```

##Features:
* memcached based
* data group control