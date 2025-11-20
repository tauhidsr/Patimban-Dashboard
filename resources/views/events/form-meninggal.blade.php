<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Form Peristiwa Meninggal
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

                {{-- Form Meninggal --}}
                <form action="{{ route('events.meninggal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <h3 class="mb-2 text-lg font-semibold text-gray-700">
                        Identitas Penduduk
                    </h3>

                    {{-- No KK --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">No KK</label>
                        <input type="text" name="no_kk" class="w-full border-gray-300 rounded">
                    </div>

                    {{-- NIK --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">NIK</label>
                        <input type="text" name="nik" class="w-full border-gray-300 rounded">
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Nama</label>
                        <input type="text" name="nama" class="w-full border-gray-300 rounded">
                    </div>

                    <h3 class="pt-4 mb-2 text-lg font-semibold text-gray-700">
                        Detail Peristiwa Meninggal
                    </h3>

                    {{-- Tanggal Peristiwa --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Tanggal Peristiwa</label>
                        <input type="date" name="tanggal_peristiwa" class="w-full border-gray-300 rounded">
                    </div>

                    {{-- Tanggal Lapor --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Tanggal Lapor</label>
                        <input type="date" name="tanggal_lapor" class="w-full border-gray-300 rounded">
                    </div>

                    {{-- Tempat Meninggal --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Tempat Meninggal</label>
                        <input type="text" name="tempat_meninggal" class="w-full border-gray-300 rounded">
                    </div>

                    {{-- Jam Kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Jam Kematian</label>
                        <input type="time" name="jam_kematian" class="w-full border-gray-300 rounded">
                    </div>

                    {{-- Penyebab Kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Penyebab Kematian</label>
                        <select name="penyebab_kematian" class="w-full border-gray-300 rounded">
                            <option value="">-- pilih --</option>
                            <option value="sakit_biasa_tua">Sakit Biasa / Tua</option>
                            <option value="wabah_penyakit">Wabah Penyakit</option>
                            <option value="kecelakaan">Kecelakaan</option>
                            <option value="kriminalitas">Kriminalitas</option>
                            <option value="bunuh_diri">Bunuh Diri</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- Yang Menyatakan Kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Yang Menyatakan Kematian</label>
                        <select name="yang_menyatakan_kematian" class="w-full border-gray-300 rounded">
                            <option value="">-- pilih --</option>
                            <option value="dokter">Dokter</option>
                            <option value="tenaga_kesehatan">Tenaga Kesehatan</option>
                            <option value="kepolisian">Kepolisian</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- Nomor Akta Kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Nomor Akta Kematian</label>
                        <input type="text" name="nomor_akta_kematian" class="w-full border-gray-300 rounded">
                    </div>

                    {{-- File Akta Kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Upload Akta Kematian (Opsional)</label>
                        <input type="file" name="file_akta_kematian_path" class="w-full border-gray-300 rounded">
                    </div>

                    {{-- Catatan Peristiwa --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Catatan Peristiwa</label>
                        <textarea name="catatan_peristiwa" class="w-full border-gray-300 rounded"></textarea>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                            Simpan Peristiwa
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
