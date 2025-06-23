<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMotorCycleRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'maker_code' => 'required|integer|exists:maker,code',
            'model_code' => 'required|integer|exists:model,code',
            'type' => 'required|boolean',
            'ippan_kakaku' => 'nullable|integer|min:0',
            'nensiki' => [
                'nullable',
                'integer',
                'min:1800',
                'max:' . date('Y'),
            ],
            'soukou' => 'nullable|integer|min:0|max:9999999',
            'soukou_fumei_flg' => 'nullable|integer',
            'haikiryo' => 'nullable|max:3000|min:49|integer',
            'color_code' => 'nullable|string|max:2',
            'color' => 'nullable|string|max:64',
            'noridasi_kakaku' => 'nullable|integer',
            'iyoukyo' => 'required|integer',
            'grade' => 'nullable|string|max:255',
            'shatai_bangou' => 'nullable|string|max:255',
            'first_year_registration' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . date('Y'),
            ],
            'last_update_id' => 'nullable|string|max:20',

        ];
    }
    public function messages()
    {
        return [
            'maker_code.required' => 'Vui lòng chọn hãng xe.',
            'maker_code.integer' => 'Mã hãng xe phải là số.',
            'maker_code.exists' => 'Hãng xe không tồn tại.',

            'model_code.required' => 'Vui lòng chọn dòng xe.',
            'model_code.integer' => 'Mã dòng xe phải là số.',
            'model_code.exists' => 'Dòng xe không tồn tại.',

            'type.required' => 'Vui lòng chọn loại xe.',
            'type.boolean' => 'Loại xe không hợp lệ.',

            'ippan_kakaku.integer' => 'Giá bán phải là số.',
            'ippan_kakaku.min' => 'Giá bán không được âm.',

            'nensiki.integer' => 'Năm sản xuất phải là số.',
            'nensiki.min' => 'Năm sản xuất không hợp lệ.',
            'nensiki.max' => 'Năm sản xuất không thể lớn hơn năm hiện tại.',

            'soukou.integer' => 'ODO phải là số.',
            'soukou.min' => 'ODO không được âm.',
            'soukou.max' => 'ODO vượt quá giới hạn cho phép.',

            'soukou_fumei_flg.integer' => 'Trạng thái ODO không xác định phải là số.',

            'haikiryo.integer' => 'Dung tích xi-lanh phải là số.',
            'haikiryo.min' => 'Dung tích tối thiểu là 49cc.',
            'haikiryo.max' => 'Dung tích tối đa là 3000cc.',

            'color_code.string' => 'Mã màu phải là chuỗi.',
            'color_code.max' => 'Mã màu không được vượt quá 2 ký tự.',

            'color.string' => 'Tên màu phải là chuỗi.',
            'color.max' => 'Tên màu không được vượt quá 64 ký tự.',

            'noridasi_kakaku.integer' => 'Giá ra biển phải là số.',

            'iyoukyo.required' => 'Vui lòng chọn trạng thái sử dụng.',
            'iyoukyo.boolean' => 'Trạng thái sử dụng không hợp lệ.',

            'grade.string' => 'Hạng xe phải là chuỗi.',
            'grade.max' => 'Hạng xe không được vượt quá 255 ký tự.',

            'shatai_bangou.string' => 'Số khung phải là chuỗi.',
            'shatai_bangou.max' => 'Số khung không được vượt quá 255 ký tự.',

            'first_year_registration.integer' => 'Năm đăng ký đầu tiên phải là số.',
            'first_year_registration.min' => 'Năm đăng ký phải từ năm 1900 trở đi.',
            'first_year_registration.max' => 'Năm đăng ký không được lớn hơn năm hiện tại.',

            'last_update_id.string' => 'ID người cập nhật phải là chuỗi.',
            'last_update_id.max' => 'ID người cập nhật không được vượt quá 20 ký tự.',

        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $nensiki = $this->input('nensiki');
            $firstYearReg = $this->input('first_year_registration');

            if (!is_null($nensiki) && !is_null($firstYearReg) && $firstYearReg > $nensiki) {
                $validator->errors()->add(
                    'first_year_registration',
                    'Năm đăng ký đầu tiên không được lớn hơn năm sản xuất.'
                );
            }
        });
    }
}
