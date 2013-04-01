<?php
function cachedf_flush($lst_index)
{
    // pega cada grupo e invalida seus itens
    $lst_index = is_array($lst_index)? $lst_index: array($lst_index);
    foreach ($lst_index as $index)
    {
        $index_key = md5("index cachedf $index");
        $index_key = $index;
        if (apc_exists($index_key))
        {
            $keys = unserialize(apc_fetch($index_key));
            foreach ($keys as $item) {
                apc_delete($item);
            }
            apc_delete($index_key);
        }
    }
}

function cachedf_set($lst_index, $key, $value) 
{
    // coloca em cache
    apc_store($key, serialize($value));

    // adiciona ao grupo(s)
    $lst_index = is_array($lst_index)? $lst_index: array($lst_index);
    foreach ($lst_index as $index)
    {
        $index_key = md5("index cachedf $index");
        $index_key = $index;
        $keys = apc_exists($index_key)? unserialize(apc_fetch($index_key)): array();
        $keys[] = $key;
        apc_store($index_key, serialize($keys));
    }   
}

// cachedf([$lst_index][, monitor_vars])
function cachedf()
{
    // argumentos
    $lst_index = (func_num_args() >= 1)? func_get_arg(0): array();
    $monitor_vars = (func_num_args() >= 2)? func_get_arg(1): array();
    $lst_index = is_array($lst_index)? $lst_index: array($lst_index);
    
    // information about caller
    $dbt = debug_backtrace();
    
    // key
    $file = array_key_exists("file", $dbt[0])? $dbt[0]["file"]: $dbt[2]["file"];
    $key = md5(serialize(array(
        $dbt[1]["function"], 
        $dbt[1]["args"], 
        filemtime($file), 
        $file,
        $monitor_vars
    )));

    // try cache
    if (apc_exists($key)) {
        $result = unserialize(apc_fetch($key));
    }
    else
    {
        // we have on "re-call the caller"?
        global $__cachedf_sem;
        if (!isset($__cachedf_sem)) {
            $__cachedf_sem = array();
        }
        if ($__cachedf_sem[$key]) {
            return  false;
        }
        
        // not in cache, re-call the caller
        $__cachedf_sem[$key] = true;
        $result = call_user_func_array($dbt[1]["function"], $dbt[1]["args"]);
        $__cachedf_sem[$key] = false;
        cachedf_set($lst_index, $key, $result);
    }
    
    // send to cachedf_val()
    global $__cachedf_val;
    if (!isset($__cachedf_val)) {
        $__cachedf_val = array();
    }
    $__cachedf_val[] = $result;
    
    return  true;
}

function cachedf_val()
{
    global $__cachedf_val;
    return  array_pop($__cachedf_val);
}

?>