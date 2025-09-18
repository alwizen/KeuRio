<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicTransactionController extends Controller
{
    public function create(): View
    {
        return view('public.transactions.create', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StorePublicTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Select kosong kirim "" → ubah ke null agar lolos nullable|exists
        if (($data['category_id'] ?? '') === '') {
            $data['category_id'] = null;
        }

        $data['amount'] = round((float) $data['amount'], 2);

        \App\Models\Transaction::create($data);

        return redirect()
            ->route('public.transactions.create')
            ->with('success', 'Transaksi berhasil disimpan 👍');
    }
}
