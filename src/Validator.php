<?php

namespace Hexlet\Project;

class Validator
{
    public function Validate(string $url)
    {
        $errors = [];

        if (!preg_match('/http(s)\/\/:[a-zA-Z.-]*.[a-zA-Z]*', $url['name'])) {
            $errors[] = 'Некорректный URL';
        }
        return $errors;
    }

}
