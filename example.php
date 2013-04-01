<?php
include_once "cachedf.php";

function soma($a, $b)
{
	if (cachedf()) return cachedf_val(); // ALWAYS ADD AT FIRST LINE OF FUNCTION
	
	return	$a + $b;
}

$a = rand()%1000000;
$b = rand()%1000000;
echo soma(soma($a, $b), soma(1,soma($a,$b)));
?>