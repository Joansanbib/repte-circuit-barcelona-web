<!-- <style>
    @font-face {
    font-family: FontSpecial;
    src: url(public/fonts/Bowlby_One_SC/BowlbyOneSC-Regular.ttf);
    }
  
    *{
        font-family: FontSpecial;
    }
</style> -->
<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Informació del perfil') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Actulitzar informació del perfil') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Nom') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />
            

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('La teva adreça mail no està verificada.') }}

                    <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                        {{ __('Clica aquí per tornar a reenviar el mail de verificació.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('El mail de verificació ha estat enviat amb exit.') }}
                    </p>
                @endif
            @endif
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="NIF" value="{{ __('NIF') }}" />
            <x-input id="NIF" type="text" class="mt-1 block w-full" wire:model="state.NIF" required/>
            <x-input-error for="NIF" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="Nom" value="{{ __('Nom') }}" />
            <x-input id="Nom" type="text" class="mt-1 block w-full" wire:model="state.Nom" required/>
            <x-input-error for="Nom" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="Cognoms" value="{{ __('Cognoms') }}" />
            <x-input id="Cognoms" type="text" class="mt-1 block w-full" wire:model="state.Cognoms" required/>
            <x-input-error for="Cognoms" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="Data_naixament" value="{{ __('Data naixament') }}" />
            <x-input id="Data_naixament" type="date" class="mt-1 block w-full" wire:model="state.Data_naixament" required/>
            <x-input-error for="Data_naixament" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Guardat.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>
