<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Peserta') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ registrationModal: false, comp: {}, paymentMethod: 'manual', merchModal: false, activeMerch: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    @include('user.partials.alerts')
                    @include('user.partials.profile-banner')
                    @include('user.partials.registered-competitions')
                    @include('user.partials.available-competitions')
                </div>

                <div class="space-y-8">
                    @include('user.partials.announcements')
                    @include('user.partials.merchandise')
                    @include('user.partials.help-support')
                </div>

            </div>
        </div>

        @include('user.partials.modal-registration')
        @include('user.partials.modal-merchandise')
        
    </div>
</x-app-layout>