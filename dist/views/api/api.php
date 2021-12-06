<?php
header('Content-Type: application/json');
http_response_code((int) $response['status']);
print (json_encode($response));