<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PrinterNameRule implements Rule
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

            $printer_name = request()->input('printer_name');

            if ($printer_name) {

                if (strlen($printer_name) > 255) {
                    $this->message = 'Printer Name must be less than 255 characters';
                    $passed = false;
                }

            } else {
                $this->message = 'Printer Name is required';
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
