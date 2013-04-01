<?php
include_once "cachedf.php";

function sum($a, $b)
{
    if (cachedf()) return cachedf_val(); // ALWAYS ADD THIS AT FIRST LINE OF FUNCTION
    
    // simulate expensive code
    sleep(2);
    
    return  $a + $b;
}

$a = rand()%1000000;
$b = rand()%1000000;

// benchmark: we'll call 2 times - first without cache, then with cache:
$time_start = microtime(true);
$r1 = sum(sum($a, $b), sum(1, sum($a, $b)));
$time_end_1 = microtime(true);
$r2 = sum(sum($a, $b), sum(1, sum($a, $b)));
$time_end_2 = microtime(true);

// benchmark result:
echo ($r1 == $r2)? '$r1 and $r2 are equals<br />': 'something has got wrong! $r1 and $r2 aren\'t equals<br />';
echo "1st call (without cache): ". ($time_end_1 - $time_start) ." seconds<br />";
echo "2nd call (with cache): ". ($time_end_2 - $time_end_1) ." seconds<br />";

// output:
// $r1 and $r2 are equals
// 1st call (without cache): 6.0010 seconds
// 2nd call (with cache): 0.0001 seconds
?>