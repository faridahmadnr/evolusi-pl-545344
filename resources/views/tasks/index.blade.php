<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evolusi PL - Task Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen py-10 px-4">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <header class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full mb-2">
                        Tugas 1 Evolusi Perangkat Lunak
                    </span>
                    <h1 class="text-2xl font-bold text-slate-800">Manajemen Daftar Tugas (Task Manager)</h1>
                    <p class="text-slate-500 text-sm mt-1">NIM: 545344 &bull; Branch: <code class="bg-slate-100 px-2 py-0.5 rounded text-indigo-600 font-mono text-xs">feature/todo-management</code></p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-lg border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> CI Ready
                    </span>
                </div>
            </div>
        </header>

        <!-- Flash Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm shadow-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Tambah Tugas -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Tambah Tugas Baru</h2>
            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Judul Tugas <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" id="title" required placeholder="Contoh: Implementasi Unit Testing di CI" value="{{ old('title') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-slate-800 transition">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" rows="2" placeholder="Catatan atau detail tugas..."
                              class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-slate-800 transition">{{ old('description') }}</textarea>
                </div>
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition shadow-sm hover:shadow">
                    + Simpan Tugas
                </button>
            </form>
        </div>

        <!-- Daftar Tugas -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-800">Daftar Tugas</h2>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full">
                    Total: {{ $tasks->count() }}
                </span>
            </div>

            @if($tasks->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-sm">Belum ada tugas. Silakan tambahkan tugas pertama Anda di atas!</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($tasks as $task)
                        <div class="py-4 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="pt-0.5">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Klik untuk ubah status"
                                            class="w-5 h-5 rounded border flex items-center justify-center transition {{ $task->is_completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-indigo-500 text-transparent' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </form>
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold {{ $task->is_completed ? 'line-through text-slate-400' : 'text-slate-800' }}">
                                        {{ $task->title }}
                                    </h3>
                                    @if($task->description)
                                        <p class="text-xs mt-1 {{ $task->is_completed ? 'line-through text-slate-400' : 'text-slate-500' }}">
                                            {{ $task->description }}
                                        </p>
                                    @endif
                                    <span class="inline-block text-[11px] text-slate-400 mt-2">
                                        Dibuat {{ $task->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $task->is_completed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $task->is_completed ? 'Selesai' : 'Pending' }}
                                </span>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-500 p-1 transition" title="Hapus tugas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</body>
</html>
