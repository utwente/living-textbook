<?php

$content = file_get_contents('../update/content/update.html');
http_response_code(503);
echo $content;
exit();
