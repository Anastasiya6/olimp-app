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
                        @livewire('designation-search-dropdown')

                        <livewire:material-search-dropdown :material_id="null" :material_name="null"/>

                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Норма</span>
                                <input type="text" name="norm" class="block w-full mt-1 rounded-md" placeholder=""
                                       value="{{ old('norm') }}" />
                            </label>
                            @error('norm')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Виберіть цех</span>
                                <select name="department_id" class="block w-full mt-1 rounded-md">
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"  @if($department->number==$default_department) selected @endif>
                                            {{ $department->number }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            @error('department_id')
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
