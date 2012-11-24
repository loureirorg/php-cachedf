<?php
define('HOST_MEMCACHE', 'localhost:11211');

$cached_val = array();
$cached_sem = array();
$mem_cache = null;

function server_cache()
{
	global $mem_cache;
	if (!isset($mem_cache))
	{
		$mem_cache = new Memcache;
		$mem_cache->addServer(HOST_MEMCACHE);
	}
	return	$mem_cache;
}

function flush_cache($group)
{
	$mem = server_cache();
	
	// $group é um nome de grupo ou uma sequencia deles
	if (!is_array($group)) {
		$group = array($group);
	}
	
	// pega cada grupo e invalida seus itens
	foreach ($group as $one_group)
	{
		$lst_group = $mem->get(md5("group memcache ".$one_group));
		if ($lst_group !== false) 
		{
			foreach($lst_group as $item) {
				$mem->delete($item);
			}
		}
	}
}

function set_cache($group, $chave, $valor, $tempo = 60) 
{
	$mem = server_cache();

	// $group é um nome de grupo ou uma sequencia deles
	$group = (!is_array($group))?array($group):$group;
	
	// adiciona ao grupo(s)
	foreach ($group as $one_group)
	{
		$lst_group = $mem->get(md5("group memcache ".$one_group));
		$lst_group = ($lst_group === false) ? array() : $lst_group;
		$lst_group[] = $chave;
		$mem->set(md5("group memcache ".$one_group), $lst_group);
	}
	
	// coloca em cache
	$mem->set($chave, $valor, 0, $tempo);
}

function get_cache($chave)
{
	$mem = server_cache();
	return	$mem->get($chave);
}

function cachedf()
{
	// arguments
	$groups = (func_num_args() >= 1)?func_get_arg(0):array();
	$monitor_vars = (func_num_args() >= 2)?func_get_arg(1):array();
	
	// transform into array
	$groups = (is_array($groups))?$groups:array($groups);
	
	// information about caller
	$dbt = debug_backtrace();
	
	// key
	$key = md5(json_encode(array($dbt[1]["function"], $dbt[1]["args"], array_key_exists("file", $dbt[1])?$dbt[1]["file"]:$dbt[3]["file"], $monitor_vars)));

	// we have on "re-call the caller"?
	global $cached_sem;
	if ($cached_sem[$key]) return	false;
	
	// try cache: if don't, re-call the caller
	$result = get_cache($key);
	if ($result === false)
	{
		$cached_sem[$key] = true;
		$result = call_user_func_array($dbt[1]["function"], $dbt[1]["args"]);
		$cached_sem[$key] = false;
		set_cache($group, $key, $result, 0);
	}
	
	// send to cachedf_val()
	global $cached_val;
	$cached_val[] = $result;
	
	return	true;
}

function cachedf_val()
{
	global $cached_val;
	return	array_pop($cached_val);
}

?>