<?php
@set_time_limit(0);


$output = shell_exec('ls -lart');

echo "<pre>$output</pre>";

shell_exec ("/data/WWWROOT/shoemarker/hott/public/batch/run_get_erp_stock.sh &");
?>