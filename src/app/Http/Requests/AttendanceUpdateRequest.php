<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
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


    // 基本バリデーション
    public function rules(): array
    {
        return [
            'clock_in'  => ['required', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],
            'note'      => ['required'],
        ];
    }


    // 時刻の前後関係チェック
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clockIn = $this->clock_in ? Carbon::createFromFormat('H:i', $this->clock_in) : null;

            $clockOut = $this->clock_out ? Carbon::createFromFormat('H:i', $this->clock_out) : null;

            // ① 出勤 ≧ 退勤（退勤入力済の場合のみ）
            if ($clockIn && $clockOut && $clockIn->greaterThanOrEqualTo($clockOut)) {
                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            // ②+③ 休憩前後チェック（既存レコード）
            foreach ($this->break_start ?? [] as $restId => $startStr) {
                $breakStart = $startStr ? Carbon::createFromFormat('H:i', $startStr) : null;

                $endStr     = $this->break_end[$restId] ?? null;

                $breakEnd   = $endStr ? Carbon::createFromFormat('H:i', $endStr) : null;

                // ② 休憩開始が出勤前 または 退勤後
                if ($breakStart && $clockIn && $breakStart->lessThan($clockIn)) {
                    $validator->errors()->add('break_start', '休憩時間が不適切な値です');
                }

                if ($breakStart && $clockOut && $breakStart->greaterThan($clockOut)) {
                    $validator->errors()->add('break_start', '休憩時間が不適切な値です');
                }

                // ③ 休憩終了が退勤後
                if ($breakEnd && $clockOut && $breakEnd->greaterThan($clockOut)) {
                    $validator->errors()->add('break_end', '休憩もしくは退勤時間が不適切な値です');
                }
            }

            // ②+③ 新規休憩フィールドにも同じチェック
            $newStart = $this->break_start_new ? Carbon::createFromFormat('H:i', $this->break_start_new) : null;

            $newEnd = $this->break_end_new     ? Carbon::createFromFormat('H:i', $this->break_end_new)   : null;

            if ($newStart && $clockIn && $newStart->lessThan($clockIn))
            { $validator->errors()->add('break_start_new', '休憩時間が不適切な値です'); }

            if ($newStart && $clockOut && $newStart->greater->greaterThan($clockOut))
            { $validator->errors()->add('break_start_new', '休憩時間が不適切な値です'); }

            if ($newEnd && $clockOut && $newEnd->greaterThan($clockOut))
            { $validator->errors()->add('break_end_new', '休憩時間もしくは退勤時間が不適切な値です'); }
        });
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'note.required'     => '備考を記入してください',
        ];
    }
}
