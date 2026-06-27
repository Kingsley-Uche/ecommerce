<div class="row">

    <div class="col-lg-6">
        <div class="mb-3">
            <label>Name</label>
            <input
                class="form-control"
                name="products[{{ $index }}][name]"
                type="text">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-3">
            <label>Price</label>
            <input
                class="form-control"
                name="products[{{ $index }}][price]"
                type="number"
                step="0.01">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-3">
            <label>Categories</label>

           <select
    class="form-select"
    multiple
    name="products[{{ $index }}][categories][]">

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

            <input
                class="form-control"
                name="products[{{ $index }}][stock]"
                type="number">
        </div>
    </div>

    <div class="col-12">

        <div class="mb-3">
            <label>Description</label>

            <textarea
                class="form-control"
                rows="3"
                name="products[{{ $index }}][description]"></textarea>
        </div>

    </div>

    <div class="col-12">

        <label>Images</label>

        <div class="image-wrapper">

            <div class="input-group mb-2">

                <input
                    type="file"
                    class="form-control"
                    name="products[{{ $index }}][images][]">

                <button
                    type="button"
                    class="btn btn-success add-image">
                    +
                </button>

            </div>

        </div>

    </div>

</div>