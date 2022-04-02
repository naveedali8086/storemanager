<?php

namespace App\Http\Requests;

use App\Rules\OperatingSystemRule;
use App\Rules\PrinterNameRule;
use Illuminate\Foundation\Http\FormRequest;

class ShopStoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'bail|required|max:50',
            'city' => ['bail', 'required'],
            'address' => 'bail|required|max:255',
            'printer_name' => new PrinterNameRule(),
            'operating_system' => new OperatingSystemRule(),
            'time_zone_id' => 'bail|required|exists:time_zones,id',
            'dialing_code' => 'bail|required_with:contact_number|nullable|exists:countries,dialing_code',
            'contact_number' => 'bail|required|max:10|min:10',
        ];
    }
}
