<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Створити запис') }}
        </h2>
    </x-slot>

    <div class="py-12 search-box">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 w-full">
                    <form method="POST" action="{{ route($route.'.store') }}">
                        @csrf
                        <livewire:designation-search-dropdown :designation_hidden="'designation_id'" :designation_title="'Деталь'" :designation_name="'designation'" last_record="App\Models\DesignationMaterial"/>

                        <livewire:material-search-dropdown :material_id="null" :material_name="null" last_record="App\Models\DesignationMaterial"/>

                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="block">
                                    <span class="text-gray-700">Норма</span>
                                    <input type="text" name="norm" class="block w-full py-3 px-4 border bg-gray-200 border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:shadow-outline-blue sm:text-sm transition duration-150 ease-in-out" placeholder=""
                                           value="{{ old('norm') }}" />
                                </label>
                                @error('norm')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">

                                <label class="block">
                                    <span class="text-gray-700">Виберіть цех</span>
                                    <select name="department_id" class="block w-full py-3 px-4 border bg-gray-200 border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:shadow-outline-blue sm:text-sm transition duration-150 ease-in-out">
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
                        </div>
                        <div class="flex justify-center">
                            <x-primary-button type="submit">
                                Зберегти
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
