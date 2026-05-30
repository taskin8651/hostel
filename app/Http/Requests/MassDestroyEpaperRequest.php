<?php

namespace App\Http\Requests;

use App\Models\Epaper;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyEpaperRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('epaper_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:epapers,id',
        ];
    }
}
