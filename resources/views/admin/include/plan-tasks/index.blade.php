<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('План') }}
        </h2>
    </x-slot>
    <livewire:plan-task-table :selectedOrder="$order_name_id" :sender_department_id="$sender_department_id" :receiver_department_id="$receiver_department_id"/>
</x-app-layout>
