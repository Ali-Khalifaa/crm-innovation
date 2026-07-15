<?php
namespace App\Http\Requests\Website;
use Illuminate\Foundation\Http\FormRequest;
class LandingContactRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'message' => 'required|string|min:10|max:2000',
        ];
    }
}
