<x-app-layout>

<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
إضافة منتج
</h2>
</x-slot>

<div class="p-6">

<form method="POST" action="{{ route('products.store') }}">

@csrf

<div style="margin-bottom:15px">
<label>الاسم</label>
<input type="text" name="name" style="border:1px solid #ccc;padding:8px;width:100%">
</div>

<div style="margin-bottom:15px">
<label>الوصف</label>
<textarea name="description" style="border:1px solid #ccc;padding:8px;width:100%"></textarea>
</div>

<div style="margin-bottom:15px">
<label>السعر</label>
<input type="text" name="price" style="border:1px solid #ccc;padding:8px;width:100%">
</div>

<button type="submit" style="background:green;color:white;padding:10px;border-radius:5px">
حفظ المنتج
</button>

</form>

</div>

</x-app-layout>