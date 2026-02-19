<?php

class Response
{
    public static function error($errors, $code = 400)
    {
        http_response_code($code);

        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        return;

    }

    public static function success($data = null, $message = null)
    {

        echo json_encode([
            'success' => true,
            'data' => $data,
            'message' => $message
        ]);
        return;
    }
}
?>