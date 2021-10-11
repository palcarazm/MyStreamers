<?php
http_response_code((int) $response['status']);
print (json_encode($response));