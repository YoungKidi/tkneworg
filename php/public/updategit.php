<?php
putenv('LANG=C.UTF-8');    
//$result = shell_exec('cd /mydata/wwwroot/51menke && svn update --accept theirs-full /mydata/wwwroot/51menke 2>&1');    
$result = shell_exec("cd /home/wwwroot/tuoke && /usr/bin/git pull ");   
echo nl2br($result);


?>
