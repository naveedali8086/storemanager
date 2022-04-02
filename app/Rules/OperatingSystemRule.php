<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OperatingSystemRule implements Rule
{
    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $passed = true;

        if (request()->input('print_on_sale') || request()->input('print_on_purchase')) {

            $operating_system = request()->input('operating_system');

            if ($operating_system) {

                if (!in_array($operating_system, ['Windows', 'Mac', 'Linux', 'Unix'])) {
                    $this->message = 'Operating System is not valid';
                    $passed = false;
                }

            } else {
                $this->message = 'Operating System is required';
                $passed = false;
            }

        }

        return $passed;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
