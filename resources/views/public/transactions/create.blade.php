<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Input Transaksi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          animation: {
            'fade-in': 'fadeIn 0.3s ease-in-out',
            'slide-up': 'slideUp 0.3s ease-out'
          }
        }
      }
    }
  </script>
  <style>
    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-input:focus {
      transform: translateY(-1px);
      transition: all 0.2s ease;
    }
  </style>
</head>

<body class="bg-slate-50 min-h-screen">
  <!-- Navbar -->
  <nav class="bg-white border-b border-slate-200 sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
              </path>
            </svg>
          </div>
          <h1 class="text-xl font-semibold text-slate-900">Keuangan RBJ</h1>
        </div>
        <div class="flex items-center space-x-4">
          <a href="/"
            class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors duration-200">

            Admin Panel
          </a>
        </div>
      </div>
    </div>
  </nav>

  <div class="max-w-2xl mx-auto py-8 px-4">
    <!-- Header Section -->
    <div class="text-center mb-8 animate-fade-in">
      <h2 class="text-3xl font-bold text-slate-900 mb-2">Input Transaksi</h2>
      <p class="text-slate-600">Catat pemasukan dan pengeluaran Anda dengan mudah</p>
      <div
        class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
        <div class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></div>
        Form publik (tanpa login)
      </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
      <div class="mb-6 animate-slide-up">
        <div class="rounded-xl bg-emerald-50 text-emerald-800 px-5 py-4 border-l-4 border-emerald-400">
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
          </div>
        </div>
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-6 animate-slide-up">
        <div class="rounded-xl bg-red-50 text-red-800 px-5 py-4 border-l-4 border-red-400">
          <div class="flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"></path>
            </svg>
            <div>
              <p class="font-medium mb-2">Terdapat kesalahan:</p>
              <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      </div>
    @endif

    <!-- Form -->
    <form action="{{ route('public.transactions.store') }}" method="POST"
      class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden animate-slide-up">
      @csrf

      <!-- Honeypot -->
      <div style="display:none;">
        <label for="website">Website</label>
        <input type="text" name="website" id="website" value="">
      </div>

      <div class="p-8 space-y-6">
        <!-- Transaction Type -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-3">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m0 0V6a2 2 0 002 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V4z">
              </path>
            </svg>
            Tipe Transaksi
          </label>
          <select name="type" required
            class="form-input w-full rounded-xl border-slate-300 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 transition-all duration-200 py-3 px-4">
            <option value="income" @selected(old('type') === 'income')>💰 Income (Pemasukan)</option>
            <option value="expense" @selected(old('type') === 'expense')>💸 Expense (Pengeluaran)</option>
          </select>
        </div>

        <!-- Date & Amount -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-3">
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3a4 4 0 118 0v4m-4 8a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Tanggal
            </label>
            <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}"
              required
              class="form-input w-full rounded-xl border-slate-300 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 transition-all duration-200 py-3 px-4" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-3">
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                </path>
              </svg>
              Jumlah (Rp)
            </label>
            <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" required
              placeholder="Contoh: 150000"
              class="form-input w-full rounded-xl border-slate-300 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 transition-all duration-200 py-3 px-4">
          </div>
        </div>

        <!-- Title -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-3">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
              </path>
            </svg>
            Judul Transaksi
          </label>
          <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Gaji, Sarapan, BBM"
            class="form-input w-full rounded-xl border-slate-300 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 transition-all duration-200 py-3 px-4">
        </div>

        <!-- Category -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-3">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 11H5m14-7l2 8-2 8M5 4l2 8-2 8"></path>
            </svg>
            Kategori
          </label>
          <select name="category_id"
            class="form-input w-full rounded-xl border-slate-300 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 transition-all duration-200 py-3 px-4">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- Note -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-3">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
              </path>
            </svg>
            Catatan <span class="text-slate-400 font-normal">(Opsional)</span>
          </label>
          <textarea name="note" rows="3" placeholder="Tambahkan catatan jika diperlukan..."
            class="form-input w-full rounded-xl border-slate-300 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 transition-all duration-200 py-3 px-4 resize-none">{{ old('note') }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
          <button type="submit"
            class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 text-white font-semibold px-6 py-4 hover:bg-indigo-700 focus:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Simpan Transaksi
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-slate-50 px-8 py-4 border-t border-slate-100">
        <p class="text-xs text-slate-500 text-center flex items-center justify-center">
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
              clip-rule="evenodd"></path>
          </svg>
          Form ini dapat diakses tanpa login untuk kemudahan input
        </p>
      </div>
    </form>

    <!-- Quick Stats or Tips Section -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center mb-2">
          <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"></path>
            </svg>
          </div>
          <h3 class="font-semibold text-slate-900">Mudah & Cepat</h3>
        </div>
        <p class="text-sm text-slate-600">Input transaksi hanya dalam beberapa detik tanpa registrasi</p>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center mb-2">
          <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                clip-rule="evenodd"></path>
            </svg>
          </div>
          <h3 class="font-semibold text-slate-900">Terorganisir</h3>
        </div>
        <p class="text-sm text-slate-600">Kategorisasi otomatis untuk laporan yang lebih rapi</p>
      </div>
    </div>
  </div>

  <script>
    // Add smooth focus transitions
    document.querySelectorAll('.form-input').forEach(input => {
      input.addEventListener('focus', function () {
        this.parentElement.classList.add('focused');
      });

      input.addEventListener('blur', function () {
        this.parentElement.classList.remove('focused');
      });
    });

    // Auto-format number input
    const amountInput = document.querySelector('input[name="amount"]');
    if (amountInput) {
      amountInput.addEventListener('input', function () {
        // Remove any non-digit characters except decimal point
        let value = this.value.replace(/[^\d.]/g, '');
        this.value = value;
      });
    }
  </script>
</body>

</html>