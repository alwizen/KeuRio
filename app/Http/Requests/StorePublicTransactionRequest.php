<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicTransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type'             => ['required', 'in:income,expense'],
            'transaction_date' => ['required', 'date'],      // <-- pakai rule 'date' BUKAN 'transaction_date'
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'title'            => ['required', 'string', 'max:255'],
            'note'             => ['nullable', 'string'],
            'category_id'      => ['nullable', 'exists:categories,id'],
            'website'          => ['nullable', 'prohibited'], // honeypot
        ];
    }

    public function attributes(): array
    {
        return [
            'type'             => 'type',
            'transaction_date' => 'date',   // tampilkan label error yang enak dibaca
            'amount'           => 'amount',
            'title'            => 'title',
        ];
    }

    public function messages(): array
    {
        return [
            'required'                     => 'Field :attribute wajib diisi.',
            'transaction_date.date'        => 'Format tanggal tidak valid.',
            'amount.min'                   => 'Amount minimal :min.',
        ];
    }
}
