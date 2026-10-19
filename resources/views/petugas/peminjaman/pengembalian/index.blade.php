@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Pemantauan & Proses Pengembalian Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <p class="font-semibold mb-1">Pengembalian belum berhasil diproses:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Daftar Pengajuan Pengembalian
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Peminjaman aktif yang sudah mengajukan pengembalian alat.
                    </p>
                </div>

                <form action="{{ route('petugas.pengembalian.index') }}" method="GET"
                    class="flex w-full md:w-80">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama peminjam..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">

                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                        Cari
                    </button>

                    @if(request('search'))
                        <a href="{{ route('petugas.pengembalian.index') }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Pengajuan</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b text-center">Aksi Pengembalian</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $item)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $item->user->name ?? 'User Dihapus' }}
                            </td>

                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_pinjam }}
                            </td>

                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ $item->tgl_kembali_plan }}
                            </td>

                            <td class="py-3 px-4 border-b">
                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold
                                    {{ $item->status === 'telat'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                @if($item->pengajuan_kembali_at)
                                    <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold bg-amber-100 text-amber-800">
                                        Menunggu Petugas
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($item->pengajuan_kembali_at)->format('d-m-Y H:i') }}
                                    </p>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-600">
                                        Belum Mengajukan
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    @forelse($item->detailPinjams as $detail)
                                        <li>
                                            <span class="font-semibold">
                                                {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                            </span>
                                            (Jumlah: {{ $detail->jumlah }})
                                        </li>
                                    @empty
                                        <li class="list-none text-gray-400">
                                            Detail alat tidak tersedia.
                                        </li>
                                    @endforelse
                                </ul>
                            </td>

                            <td class="py-3 px-4 border-b text-center">
                                @if($item->pengajuan_kembali_at)
                                    <form
                                        action="{{ route('petugas.peminjaman.kembali', $item->id) }}"
                                        method="POST"
                                        class="inline-block min-w-40 bg-gray-50 p-3 rounded border border-gray-200 text-left space-y-2">

                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                                Kondisi Kembali:
                                            </label>

                                            <select
                                                name="kondisi_kembali"
                                                required
                                                class="w-full text-xs border border-gray-300 rounded px-2 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak Ringan">Rusak Ringan</option>
                                                <option value="Rusak Berat">Rusak Berat</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                                Denda (Rp):
                                            </label>

                                            <input
                                                type="number"
                                                name="denda"
                                                value="0"
                                                min="0"
                                                step="1"
                                                required
                                                class="w-full text-xs border border-gray-300 rounded px-2 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>

                                        <button
                                            type="submit"
                                            onclick="return confirm('Yakin ingin memproses pengembalian alat ini?')"
                                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded text-xs font-semibold transition shadow-sm">
                                            Terima Pengembalian
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-500 px-3 py-2 rounded text-xs">
                                        Menunggu pengajuan peminjam
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-3xl">📋</span>
                                    <p class="font-semibold">Belum ada pengajuan pengembalian.</p>
                                    <p class="text-xs">
                                        Pengajuan dari peminjam akan muncul di halaman ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
            Total data: {{ $peminjamans->count() }} peminjaman aktif.
        </div>
    </div>
@endsection