<?php
$files = glob($_SERVER['DOCUMENT_ROOT'].'/data/cache/L*');
array_walk($files, function ($file) {
unlink($file);
});
echo "OK";

