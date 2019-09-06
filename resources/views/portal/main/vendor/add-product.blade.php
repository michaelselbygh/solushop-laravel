@extends('portal.layouts.vendor.master')

@section('page-title')Add Product @endsection

@section('content-body')
    <form method="POST" action="{{ route("vendor.process.add.product") }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-5" style="margin-top: 10px;">
                        <h5 class="card-title">Details</h5>
                    </div>
                    <div class="col-md-7" style="text-align: right; margin-bottom: 5px;">
                    </div>
                </div>
                
                @include('portal.main.success-and-error.message')
                <div class="card" style="">
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input id="name" name="name" class="form-control round" placeholder="Enter product name" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type">Availability</label>
                                            <select class="form-control" name='type' id="type" style='border-radius:7px;' required>
                                                <option value='0'>Available In Stock</option>
                                                <option value='1'>Available On Pre-Order</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="description">Description (Optional)</label>
                                            <textarea id="description" name="description" class="form-control round" placeholder="Enter Product Description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="features">Highlighted Features</label>
                                            <textarea id="features" name="features" class="form-control round" placeholder="Enter Product Highlighted Features" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select class="form-control" name='category' id="category" style='border-radius:7px;' required>
                                                @for ($i = 0; $i < sizeof($product["category_options"]); $i++)
                                                    <option value="{{ $product["category_options"][$i]["id"] }}">
                                                        {{ $product["category_options"][$i]["pc_description"] }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tags">Tags (Optional)</label>
                                            <input id="tags" name="tags" class="form-control round" placeholder="Enter product tags" value="" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="settlement_price">Settlement Price</label>
                                            <input id="settlement_price" name="settlement_price" class="form-control round" placeholder="Enter product settlement price" value="" type="number" step="0.01" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="selling_price">Selling Price </label>
                                            <input id="selling_price" name="selling_price" class="form-control round" placeholder="Enter product selling price" value="" type="number" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="discount">Discount</label>
                                            <input id="discount" name="discount" class="form-control round" placeholder="Enter product discount" value="0" type="number" step="0.01" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
            </div>
            <div class="col-md-5">
                <h5 class="card-title">Images</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                                <div class="row">
                                    <div class="col-md-12" style="text-align: center; padding-top:5px;">
                                        <input type="file" class="form-control-file" name="product_images[]" id="product_images" multiple required>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6" style="margin-top: 10px;">
                        <h5 class="card-title">Stock</h5>
                    </div>
                    <div class="col-md-6" style="text-align:right; margin-bottom: 5px;">
                        <button type="button" data-toggle="tooltip" id="addVariation" data-popup="tooltip-custom" data-original-title="Add Variation" style="margin-top: 3px;" class="btn btn-info btn-sm round">
                            <i class="ft-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card" style="">
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group" style="margin-bottom: 2px;">
                                            <label for="name">Description</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-bottom: 2px;">
                                            <label for="name">Quantity</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="variations">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <input class="form-control round" name='variantDescription0' value="None" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input class="form-control round" name='stock0' value="1" min="1" type="number" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type='hidden' id='newSKUCount' name='newSKUCount' Value='1'>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions" style="text-align:center; padding: 0px;">
                    <button type="submit" class="btn btn-success">
                            Add Product
                    </button>
                </div>
            </div>
        </div>
    </form>
    <script>

        var variationCount = document.getElementById("newSKUCount").value;
        $( "#addVariation" ).click(function(){

                var updateString = "<div class='row'><div class='col-md-8'><div class='form-group'><input class='form-control round' name='variantDescription"+variationCount+"' value='None' type='text'></div></div><div class='col-md-4'><div class='form-group'><input class='form-control round' name='stock"+variationCount+"' value='1' type='number' ></div></div></div>";
                
                //populate modal inputs
                $('#variations').append(updateString);
                variationCount++;

                document.getElementById("newSKUCount").value = variationCount;
            
        });

    </script>
@endsection

