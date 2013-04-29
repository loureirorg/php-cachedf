#php-cachedf
A PHP accelerator
[![githalytics.com alpha](https://cruel-carlota.pagodabox.com/42e4fff8057d7da519432a59d007cfc2 "githalytics.com")](http://githalytics.com/loureirorg/php-cachedf)
##How this works?
php-cachedf speeds up your php code by caching expensive functions. When you call a cached function, php-cachedf will save the result and associate it with: 
  * arguments
  * name of function
  * the file (name and inode) where the function resides
  * user-defined variables (vars that you use inside the function and which will affect the result)

the next time this function is called, cachedf will return the cached result. You always can invalidate a data (or a group of data) by calling "cachedf_flush".

##When NOT to use:
  * DON'T use: in functions which makes data updates (databases, variables, etc);
  * DON'T use: in functions which write data to stdout ("printf", "echo", ...) or send data to any resource (like files);
  * DON'T use (or use with care): in functions that return sensible information (like user profile);
  * DON'T use: in fast functions because this library has overhead so, if you do, your code will actually slow-down;

**ALWAYS keep in mind that cachedf will cache the "return" of function**

##How-to use:
  * include "cachedf.php"
  * at first line of your function include this:

```php
if (cachedf()) return cachedf_val();
```

##Features:
  * APC based
  * groups of cached functions support (to invalidate all at once ;) )

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
