<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Створити запис') }}
        </h2>
    </x-slot>

    <div class="py-12 search-box">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 max-w-lg w-full lg:max-w-xs">
                    <form method="POST" action="{{ route($route.'.store') }}">
                        @csrf
                        @livewire('group-material-search-dropdown')

                        @livewire('group-material-entry-search-dropdown')
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Норма</span>
                                <input type="text" name="norm" class="block w-full mt-1 rounded-md" placeholder=""
                                       value="{{ old('norm') }}" style="font-weight: bold;"/>
                            </label>
                            @error('norm')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-primary-button type="submit">
                            Зберегти
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
