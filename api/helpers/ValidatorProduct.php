<?php
class ValidatorProduct
{
    private static function required($data, $fields)
    {
        $errors = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = "Campo '{$field}' é obrigatório";
            }
        }

        return $errors;
    }

    public static function status($value)
    {
        return in_array((int) $value, [0, 1], true);
    }


    // Valida dados para CREATE e UPDATE de produto

    public static function validate($data)
    {
        $errors = [];

        $errors = array_merge($errors, self::required($data, ['name', 'code']));

        if (isset($data['status']) && !self::status($data['status'])) {
            $errors['status'] = 'Status deve ser 0 ou 1';
        }

        return $errors;
    }
}
?>