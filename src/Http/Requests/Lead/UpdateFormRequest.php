<?php

namespace  App\Http\Requests\Lead;

use App\Models\Lead;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {        $rules = [
            'name' => 'required|max:50',
            'password' => 'required|max:50',
            'cellphone' => 'required',
            'phone_ext' => 'required',
            'phone' => 'required|numeric|max:15',
            'address1' => 'required|max:250',
            'address2' => 'required|max:250',
            'city' => 'required',
            'state' => 'required',
            'country' =>'required',
            'status' => 'required|in:active,pending,cancelled,blocked,archived'
        ];

        if (Lead::UNIQUE_FIELD == 'email') {
            $rules['email'] = [
                'required','email','max:200',
                Rule::unique('leads')->where(function ($query){
                    return $query->where('email', $this->input('email'))->where('is_deleted',0);
                })->ignore($this->lead),
            ];
            $rules['phone'] = 'required|numeric';
        }else{
            $rules['phone'] = [
                'required','numeric',
                Rule::unique('leads')->where(function ($query){
                    return $query->where('phone', $this->input('phone'))->where('is_deleted',0);
                })->ignore($this->lead),
            ];
            $rules['email'] = 'required|email|max:200';
        }

        return $rules;
    }
    protected function failedValidation(Validator $validator)
    {
        $response = [
            'type' => 'error',
            'code' => 422,
            'message' => "Server Validation Fail",
            'errors' =>$validator->errors()
        ];

        /**
         * Return response data in json formate
         */
        throw new HttpResponseException(response()->json($response, 422));
    }


}
