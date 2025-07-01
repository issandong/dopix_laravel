@extends('layouts.app')

@section('title', 'Compléter mon profil')

@section('content')
<div class="container" style="max-width:600px;">
     <h2 class="text-3xl font-extrabold text-indigo-700 mb-8 text-center">Compléter mon profil</h2>

    @if(session('status') === 'profile-updated')
        <div style="color: green; margin-bottom: 16px;">Profil mis à jour !</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
         @method('PATCH')

        <label for="Name">Nom</label>
        <input type="text" name="name" id="Name" value="{{ old('name', $user->name) }}" required class="form-control" style="width:100%;margin-bottom:10px;">
        @error('Name') <div style="color:red;">{{ $message }}</div> @enderror

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ $user->email }}" readonly class="form-control" style="width:100%;margin-bottom:10px;">

        <label for="sport">Sport</label>
        <input type="text" name="sport" id="sport" value="{{ old('sport', $user->sport) }}" class="form-control" style="width:100%;margin-bottom:10px;">
        @error('sport') <div style="color:red;">{{ $message }}</div> @enderror

        <label for="federation">Fédération</label>
        <input type="text" name="federation" id="federation" value="{{ old('federation', $user->federation) }}" class="form-control" style="width:100%;margin-bottom:10px;">
        @error('federation') <div style="color:red;">{{ $message }}</div> @enderror

        <label for="competitionLevel">Niveau de compétition</label>
        <select name="competitionLevel" id="competitionLevel" class="form-control" style="width:100%;margin-bottom:10px;">
            <option value="">-- Choisir --</option>
            @foreach(['Olympique', 'Professionnel', 'Amateur', 'Autre'] as $level)
                <option value="{{ $level }}" @if(old('competitionLevel', $user->competitionLevel) == $level) selected @endif>{{ $level }}</option>
            @endforeach
        </select>
        @error('competitionLevel') <div style="color:red;">{{ $message }}</div> @enderror

        <label for="allergies">Allergies (ctrl+clic pour plusieurs)</label>
        @php
            $allergiesList = ['Arachides', 'Gluten', 'Lactose', 'Pollen', 'Autre'];
            $userAllergies = $user->allergies ? json_decode($user->allergies, true) : [];
        @endphp
        <select name="allergies[]" id="allergies" multiple class="form-control" style="width:100%;margin-bottom:10px;">
            @foreach($allergiesList as $item)
                <option value="{{ $item }}" @if(in_array($item, old('allergies', $userAllergies))) selected @endif>{{ $item }}</option>
            @endforeach
        </select>
        @error('allergies') <div style="color:red;">{{ $message }}</div> @enderror

        <label for="subscriptionTier">Type d'abonnement</label>
        <select name="subscriptionTier" id="subscriptionTier" class="form-control" style="width:100%;margin-bottom:10px;" disabled>
            @foreach(['Free', 'Pro'] as $tier)
                <option value="{{ $tier }}" @if(old('subscriptionTier', $user->subscriptionTier) == $tier) selected @endif>{{ $tier }}</option>
            @endforeach
        </select>
        <small>Pour changer d'abonnement, contactez le support.</small>

        <label for="subscriptionStatus">Statut d'abonnement</label>
        <select name="subscriptionStatus" id="subscriptionStatus" class="form-control" style="width:100%;margin-bottom:10px;" disabled>
            @foreach(['Active', 'Inactive', 'Expired', 'PendingPayment'] as $status)
                <option value="{{ $status }}" @if(old('subscriptionStatus', $user->subscriptionStatus) == $status) selected @endif>{{ $status }}</option>
            @endforeach
        </select>

        <label for="stripeCustomerId">Stripe Customer Id</label>
        <input type="text" name="stripeCustomerId" id="stripeCustomerId" value="{{ $user->stripeCustomerId }}" class="form-control" style="width:100%;margin-bottom:10px;" readonly>

        <div class="flex justify-center">
          <button class="cta-btn" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
@endsection