<h2>Ingrédients extraits de {{ $medicine_name }}</h2>
<ul>
@foreach($ingredients as $ingredient)
    <li>{{ is_array($ingredient) ? $ingredient['name'] : $ingredient }}</li>
@endforeach
</ul>
<form method="post" action="{{ route('manual.analyzeWithWada') }}">
    @csrf
    <input type="hidden" name="medicine_name" value="{{ $medicine_name }}">
    <input type="hidden" name="ingredients" value="{{ json_encode($ingredients) }}">
    <button type="submit">Vérifier WADA</button>
</form>