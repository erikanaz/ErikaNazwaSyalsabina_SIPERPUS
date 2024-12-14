<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex items-center w-full justify-between space-x-4">
                        <div class="flex space-x-4">
                            @hasrole('pustakawan')
                            <x-primary-button tag="a" href="{{ route('book.create') }}">Tambah Data Buku</x-primary-button>
                            <x-primary-button tag="a" href="{{ route('book.print') }}">Cetak PDF</x-primary-button>
                            <x-primary-button tag="a" href="{{ route('book.export') }}">Export Excel</x-primary-button>
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-book')" >Import Excel</x-primary-button>
                            @endhasrole
                        </div>
                    
                        <!-- Form pencarian -->
                        <form id="searchForm" method="GET" action="{{ route('book') }}" class="flex items-center ml-auto">
                            <input type="text" id="searchQuery" name="query" placeholder="Cari..." class="px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-800 dark:text-white">
                            <x-primary-button type="submit">Cari</x-primary-button>
                        </form>
                    </div>

                    <!-- Tempat untuk menampilkan hasil pencarian -->
                    <div id="searchResults" class=" ml-12">
                        <!-- Hasil pencarian akan muncul di sini -->
                    </div>

                    <x-table>
                        <x-slot name="header">
                            <tr class="py-10">
                                <th scope="col">#</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Penulis</th>
                                <th scope="col">Tahun</th>
                                <th scope="col">Penerbit</th>
                                <th scope="col">Kota</th>
                                <th scope="col">Cover</th>
                                <th scope="col">Kode Rak</th>
                                
                                @hasrole('pustakawan')
                                <th scope="col">Aksi</th>
                                @endhasrole
                            </tr>
                        </x-slot>
                        @foreach ($books as $book)
                            <tr>
                                <td>{{ $books->firstItem() + $loop->iteration - 1 }}</td>
                                <td>{{ $book->title }}</td>
                                <td>{{ $book->author }}</td>
                                <td>{{ $book->year }}</td>
                                <td>{{ $book->publisher }}</td>
                                <td>{{ $book->city }}</td>
                                <td>
                                    <img src="{{ asset('storage/cover_buku/' . $book->cover) }}" width="100px" />
                                </td>
                                <td>{{ $book->bookshelf->code }}-{{ $book->bookshelf->name }}</td>
                                <td>
                                    @hasrole('pustakawan')
                                    <x-primary-button tag="a"
                                        href="{{ route('book.edit', $book->id) }}">Edit</x-primary-button>
                                    <x-danger-button x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-book-deletion')"
                                        x-on:click="$dispatch('set-action', '{{ route('book.destroy', $book->id) }}')">{{ __('Delete') }}</x-danger-button>
                                    @endhasrole
                                </td>
                            </tr>
                        @endforeach
                    </x-table>

                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $books->appends(request()->query())->links() }}
                    </div>

                    <x-modal name="confirm-book-deletion" focusable maxWidth="xl">
                        <form method="post" x-bind:action="action" class="p-6">
                            @method('delete')
                            @csrf
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Apakah anda yakin akan menghapus data?') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Setelah proses dilaksanakan. Data akan dihilangkan secara permanen.') }}
                            </p>
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                                <x-danger-button class="ml-3">
                                    {{ __('Delete!!!') }}
                                </x-danger-button>
                            </div>
                        </form>
                    </x-modal>

                    <x-modal name="import-book" focusable maxWidth="xl">
                        <form method="post" enctype="multipart/form-data" action="{{route('book.import')}}" class="p-6">

                            @method('post')
                            @csrf
                            <x-file-input id="file" name="file" accept=".xlsx, .xls" class="mt-1 block w-full" />
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Batal') }}
                                </x-secondary-button>
                                <x-primary-button class="ml-3">
                                    {{ __('Import') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </x-modal>

                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            // Ketika form pencarian disubmit
                            $('#searchForm').on('submit', function(e) {
                                e.preventDefault(); // Mencegah reload halaman

                                var query = $('#searchQuery').val(); // Ambil nilai dari input pencarian

                                // Kirimkan permintaan AJAX ke server
                                $.ajax({
                                    url: '{{ route("search") }}',
                                    type: 'GET',
                                    data: { query: query },
                                    success: function(response) {
                                        var booksHtml = '';
                                        var authorsHtml = '';

                                        // Tampilkan hasil buku
                                        if (response.books.length > 0) {
                                            response.books.forEach(function(book) {
                                                booksHtml += '<li>' + book.title + ' oleh ' + book.author + '</li>';
                                            });
                                        } else {
                                            booksHtml = '<p>Tidak ada buku yang ditemukan.</p>';
                                        }

                                        // Gabungkan dan tampilkan hasil pencarian
                                        $('#searchResults').html(
                                            // '<h2>Buku</h2>' + 
                                            '<ul>' + booksHtml + '</ul>'
                                        );
                                    },
                                    error: function() {
                                        $('#searchResults').html('<p>Terjadi kesalahan saat melakukan pencarian.</p>');
                                    }
                                });
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
