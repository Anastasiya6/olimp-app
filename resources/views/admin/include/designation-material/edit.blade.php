<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Редагувати запис') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route($route.'.update',$designationMaterial->id) }}">
                        @csrf
                        @method('put')
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Деталь</span>
                                <input type="text" name="designation"
                                       class="block w-full mt-1 rounded-md" readonly
                                       placeholder="" value="{{old('code',$designationMaterial->designation->designation)}}" />
                            </label>
                            @error('designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <livewire:material-search-dropdown :material_id="$designationMaterial->material_id" :material_name="$designationMaterial->material->name"/>

                      {{--  <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Матеріал</span>
                                <input type="text" name="material"
                                       class="block w-full mt-1 rounded-md" readonly
                                       placeholder="" value="{{old('name',$designationMaterial->material->name)}}" />
                            </label>
                            @error('material')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>--}}
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="block">
                                    <span class="text-gray-700">Норма</span>
                                    <input type="text" name="norm"
                                           class="block w-full mt-1 rounded-md"
                                           placeholder="" value="{{old('route',$designationMaterial->norm)}}" />
                                </label>
                                @error('norm')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="block">
                                    <span class="text-gray-700">Виберіть цех</span>
                                    <select name="department_id" class="block w-full mt-1 rounded-md">
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}"
                                                @if($designationMaterial->department_id == $department->id) selected @endif 'selected' }}>
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
                                Оновити
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
