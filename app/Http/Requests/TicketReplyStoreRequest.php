<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketReplyStoreRequest extends FormRequest
{
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'content' => 'required|string',
            'status' => auth()->user()->role == 'admin' ? 'required|in:open,onprogress,resolved,rejected' : 'nullable',
        ];
    }
}
