<?php
/* Author: Gowtham */
$tmp = 'volatile.shm';

// Get the file token key
$key = ftok($tmp, 'a');
// Attach the SHM resource, notice the cast afterwards
$id = shm_attach($key);

if ($id === false) {
    die('Unable to create the shared memory segment');
}

// Cast to integer, since prior to PHP 5.3.0 the resource id 
// is returned which can be exposed when casting a resource
// to an integer
$id = (integer) $id;
echo $id;
?>
