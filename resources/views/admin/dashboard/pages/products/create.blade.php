@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Product</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.product.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
<div class="col-12">
    <div class="card">
        <div class="card-body">

            <h4 class="card-title">Create Product</h4>
            <p class="card-title-desc">Fill the form to create a product.</p>

        <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div id="product-wrapper">

        {{-- First Product --}}
        <div class="product-card card border mb-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Product #1</h5>

                    <button type="button"
                            class="btn btn-danger btn-sm remove-product"
                            style="display:none;">
                        Remove
                    </button>
                </div>

                @include('admin.dashboard.pages.products.partials.product-form', ['index' => 0])

            </div>
        </div>

    </div>

    <div class="mb-3">
        <button type="button" id="add-product" class="btn btn-success">
            + Add Another Product
        </button>

        <button type="submit" class="btn btn-primary">
            Save Products
        </button>
    </div>

</form>

        </div>
    </div>
</div>
</div>



<script>

document.addEventListener("DOMContentLoaded", function(){

    let productIndex = 1;

    const wrapper = document.getElementById('product-wrapper');

    document.getElementById('add-product').addEventListener('click', function(){

        let html = `
<div class="product-card card border mb-4">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-3">

<h5>Product #${productIndex+1}</h5>

<button
type="button"
class="btn btn-danger btn-sm remove-product">
Remove
</button>

</div>

<div class="row">

<div class="col-lg-6">
<div class="mb-3">
<label>Name</label>
<input class="form-control"
name="products[${productIndex}][name]">
</div>
</div>

<div class="col-lg-6">
<div class="mb-3">
<label>Price</label>
<input class="form-control"
type="number"
step="0.01"
name="products[${productIndex}][price]">
</div>
</div>

<div class="col-lg-6">
<div class="mb-3">
<label>Categories</label>

<select
class="form-control"
multiple
name="products[${productIndex}][categories][]">

@foreach($categories as $cat)

<option value="{{ $cat->id }}">
{{ $cat->name }}
</option>

@endforeach

</select>

</div>
</div>

<div class="col-lg-6">
<div class="mb-3">
<label>Stock</label>
<input class="form-control"
type="number"
name="products[${productIndex}][stock]">
</div>
</div>

<div class="col-12">
<div class="mb-3">
<label>Description</label>
<textarea
class="form-control"
name="products[${productIndex}][description]"></textarea>
</div>
</div>

<div class="col-12">

<label>Images</label>

<div class="image-wrapper">

<div class="input-group mb-2">

<input
type="file"
class="form-control"
name="products[${productIndex}][images][]">

<button
type="button"
class="btn btn-success add-image">
+
</button>

</div>

</div>

</div>

</div>

</div>

</div>
`;

        wrapper.insertAdjacentHTML('beforeend', html);

        productIndex++;

    });

    document.addEventListener('click',function(e){

        if(e.target.classList.contains('remove-product')){

            e.target.closest('.product-card').remove();

        }

        if(e.target.classList.contains('add-image')){

            const imageWrapper = e.target.closest('.image-wrapper');

            const inputName = e.target.previousElementSibling.name;

            imageWrapper.insertAdjacentHTML('beforeend',`
                <div class="input-group mb-2">

                    <input
                        type="file"
                        class="form-control"
                        name="${inputName}">

                    <button
                        type="button"
                        class="btn btn-danger remove-image">
                        -
                    </button>

                </div>
            `);

        }

        if(e.target.classList.contains('remove-image')){

            e.target.closest('.input-group').remove();

        }

    });

});

</script>

@endsection
