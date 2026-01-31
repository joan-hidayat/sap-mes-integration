<?php
class Validator {
    public static function checkFields(array $data, array $requiredFields): bool {
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return false;
            }
        }
        return true;
    }
}
