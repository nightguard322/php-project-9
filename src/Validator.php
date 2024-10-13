<?php

namespace Hexlet\Project;

class Validator
{
    public function Validate(array $url)
    {
        $errors = [];

        if (!preg_match('/^htt(p|ps):\/\/[a-zA-Z.-]*.[a-zA-Z]*$/', $url['name'])) {
            $errors[] = 'Некорректный URL';
        }
        return $errors;
    }

}
