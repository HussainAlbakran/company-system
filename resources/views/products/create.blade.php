<x-app-layout>

<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
{{ __('products.create_title') }}
</h2>
</x-slot>

<div class="p-6">

<form method="POST" action="{{ route('products.store') }}">

@csrf

<div style="margin-bottom:15px">
<label>{{ __('products.field_name') }}</label>
<input type="text" name="name" style="border:1px solid #ccc;padding:8px;width:100%">
</div>

<div style="margin-bottom:15px">
<label>{{ __('products.field_description') }}</label>
<textarea name="description" style="border:1px solid #ccc;padding:8px;width:100%"></textarea>
</div>

<div style="margin-bottom:15px">
<label>{{ __('products.field_price') }}</label>
<input type="text" name="price" style="border:1px solid #ccc;padding:8px;width:100%">
</div>

<button type="submit" style="background:green;color:white;padding:10px;border-radius:5px">
{{ __('products.save') }}
</button>

</form>

</div>

</x-app-layout>
