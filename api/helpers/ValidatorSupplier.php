<?php

class ValidatorSupplier
{

    // Valida campos obrigatórios

    private static function required(array $data, array $fields): array
    {
        $errors = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = "Campo '{$field}' é obrigatório";
            }
        }

        return $errors;
    }

    public static function status($value): bool
    {
        return in_array((int) $value, [0, 1], true);
    }

    public static function email($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function cnpj($value): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $value);
        return strlen($cnpj) === 14;
    }

    public static function phone($value): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $value);
        return strlen($phone) >= 10 && strlen($phone) <= 11;
    }

    public static function validate(array $data): array
    {
        $errors = [];

        $errors = array_merge($errors, self::required($data, ['name', 'cnpj', 'email', 'phone', 'status']));

        if (isset($data['name']) && strlen($data['name']) < 3) {
            $errors['name'] = 'Nome deve ter no mínimo 3 caracteres';
        }


        if (!self::cnpj($data['cnpj'])) {
            $errors['cnpj'] = 'CNPJ deve conter 14 dígitos';
        }


        if (!self::phone($data['phone'])) {
            $errors['phone'] = 'phone deve conter 14 dígitos';
        }



        if (!self::email($data['email'])) {
            $errors['email'] = 'E-mail inválido';
        }


        if (isset($data['status']) && !self::status($data['status'])) {
            $errors['status'] = 'Status deve ser 0 ou 1';
        }

        return $errors;
    }

}
?>