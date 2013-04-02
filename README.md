#php-cachedf
A PHP accelerator
[![githalytics.com alpha](https://cruel-carlota.pagodabox.com/42e4fff8057d7da519432a59d007cfc2 "githalytics.com")](http://githalytics.com/loureirorg/php-cachedf)
##How this works?
php-cachedf speeds up your site by caching expensive functions. When you call a cached function, we'll save the result and associate it with: 
* the arguments values
* the name of function
* the file of function
* others variables (user-defined)

when you call again this function (even by other php, client, day) we'll get the cached result. You always can invalidate a data (or a group of function) by calling "cachedf_flush" with the appropriate arguments.

##When NOT to use:
* DON'T use: in functions which makes data updates (databases, variables, etc);
* DON'T use: in functions which write data to stdout ("printf", "echo", ...) or send data to any resource (like files);
* DON'T use (or use with care): in functions that return sensible information (like user profile);
* DON'T use: in fast functions because this library has overhead so, if you do your site will slow-down;

ALWAYS keep in mind that cachedf will cache the "return" of function

##How-to use:
* include "cachedf.php"
* at first line of your function include this: 
```
if (cachedf()) return cachedf_val();
```

##Features:
* apc based
* data group control

##Example:
```php
<?php
include_once "cachedf.php";

function sum($a, $b)
{
    if (cachedf()) return cachedf_val(); // ALWAYS ADD THIS AT FIRST LINE OF FUNCTION
    
    // simulate expensive code
    sleep(2);
    
    return  $a + $b;
}

echo sum(10, 20). "<br />"; // without cache: this will take 2.0001 sec
echo sum(10, 20). "<br />"; // with cache: this will take 0.0001 sec
```
