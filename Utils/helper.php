<?php
function generateHttpResponse($code, $status, $message, $data = [])
{
    http_response_code($code);
    $response = [
        "Status" => $status,
        "Message" => $message,
    ];

    if (!empty($data)) {
        $response = array_merge($response, $data);
    }

    echo json_encode($response);
}
?>