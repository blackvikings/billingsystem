<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
        <!-- Styles -->

    </head>
    <body>
        <form method="POST" action="{{ route('save-order') }}">
            @csrf
            <header>
                <div class="px-3 py-2 border-bottom mb-3 p-6">
                    <div class="container d-flex flex-wrap justify-content-center">
                        <div class="col-md-4">
                            <h3>Name</h3>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="customer_name" @if(old('customer_name')) value="{{ old('customer_name') }}" @endif id="customerName">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary btn-sm" id="add_item" style="float: right;">Add Item</button>
                        </div>
                    </div>
                </div>
            </header>
            @if($errors->any())
                {!! implode('', $errors->all('<div>:message</div>')) !!}
            @endif
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('success') !!}</li>
                    </ul>
                </div>
            @endif
            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="add_item_table">
                                    @if(old('product'))
                                        @foreach(old('product') as $key => $value)
                                            <tr>
                                                <td>
                                                    <select class="form-control items" name="product[0][items]">
                                                        <option @if(empty($value['items'])) selected disabled @endif value="">Select Item</option>
                                                        @foreach($items as $item)
                                                            <option @if(!empty($value['items']) && $value['items'] == $item->id) selected @endif value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control quantity" min=0 oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" @if($value['qty']) value="{{ $value['qty'] }}" @else value="1" @endif  name="product[0][qty]">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control price" value="0.00" name="product[0][price]" disabled>
                                                </td>
                                                <td>
                                                    <span class="total_amount">0.00</span>
                                                </td>
                                            </tr>
                                        @endforeach

                                    @else
                                        <tr>
                                            <td>
                                                <select class="form-control items" name="product[0][items]" >
                                                    <option >Select Item</option>
                                                    @foreach($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control quantity" min=0 oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" value="1" name="product[0][qty]">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control price" value="0.00" name="product[0][price]" disabled>
                                            </td>
                                            <td>
                                                <span class="total_amount">0.00</span>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Total Amount</th>
                                            <th id="total_ammount">0.00 </th>
                                            <th style="display: none"><input type="text" style="display: none" name="total_amount" id="total_amount_input" /></th>
                                        </tr>
                                    </tfoot>
                                </table>


                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary" id="saveInvoice">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
        let index = 1;
        $(document).ready(function () {

            $("#add_item").on('click', function () {
                $.ajax({
                    url: '{{ route('get-items') }}',
                    type: 'GET',
                    data: {"_token": "{{ csrf_token() }}",},
                    dataType: 'json',
                    success: function (response) {

                        let customerName = $('#customerName').val();
                        // console.log(customerName);
                        if (customerName !== '' || customerName !== undefined)
                        {
                            let addRow = '<tr>'+
                                '<td>'+
                                '<select class="form-control items" name="product['+index+'][items]">' + response +
                                '</select>'+
                                '</td>'+
                                '<td>'+
                                '   <input type="text" class="form-control quantity" min=0 oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" name="product['+index+'][qty]" value="1">'+
                                '</td>'+
                                ' <td>'+
                                '<input type="text" class="form-control price" disabled name="product['+index+'][price]" value="0.00" >'+
                                '</td>'+
                                '<td>'+
                                '<span class="total_amount">0.00</span>'+
                                '</td>'+
                                '</tr>';

                            index++;

                            $('#add_item_table').append(addRow);

                            $('.quantity').removeAttr('disabled');
                            $('.price').removeAttr('disabled');
                            $('.items').removeAttr('disabled')

                            $('body').on('change', '.items', function () {
                                let itemId = $(this).val();
                                let row = $(this).closest('tr');
                                $.ajax({
                                    url: 'get-item/' + itemId,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        // Update the price input in the same row
                                        row.find('.price').val(response.price);
                                        // Optionally, you can calculate the amount based on qty and price
                                        let qty = row.find('.quantity').val();
                                        let amount = qty * response.price;
                                        row.find('.total_amount').text(amount.toFixed(2));
                                        // Update the total amount
                                        updateTotalAmount();
                                    }
                                });
                            });

                            // Calculate total amount

// Recalculate the amount when quantity changes
                            $('body').on('input', '.quantity', function () {
                                let row = $(this).closest('tr');
                                let qty = $(this).val();
                                let price = row.find('.price').val();
                                let amount = qty * price;
                                row.find('.total_amount').text(amount.toFixed(2))
                                // Update the total amount
                                updateTotalAmount();
                            })

                            $('.items').change(function () {
                                let items = [];
                                    items = $(".items :selected").map(function(i, el) {
                                                return $(el).val();
                                            }).get();

                                for (let i = 0; i < items.length - 1; i++) {
                                    if (items[i + 1] === items[i]) {
                                        $(this).prop("selectedIndex", -1).trigger("change");
                                        alert('Select Different Product');
                                    }
                                }

                            })

                        }

                    }
                })
            })

            $('body').on('change', '.items', function () {
                let itemId = $(this).val();
                let row = $(this).closest('tr');
                $.ajax({
                    url: 'get-item/' + itemId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        // Update the price input in the same row
                        row.find('.price').val(response.price);
                        // Optionally, you can calculate the amount based on qty and price
                        let qty = row.find('.quantity').val();
                        let amount = qty * response.price;
                        row.find('.total_amount').text(amount.toFixed(2));
                        // Update the total amount
                        updateTotalAmount();
                    }
                });
            });

            $('#customerName').keyup(function () {
                let customerName = $('#customerName').val();
                // console.log(customerName);
                if (customerName !== '' || customerName !== undefined)
                {

                    $('.items').removeAttr('disabled');
                    $('.quantity').removeAttr('disabled');
                    $('.price').removeAttr('disabled');
                }
            })
        })

        function updateTotalAmount() {
            let totalAmount = 0;
            $('.total_amount').each(function () {
                totalAmount += parseFloat($(this).text());
            });
            $('#total_ammount').text(totalAmount.toFixed(2));
            $('#total_amount_input').val(totalAmount.toFixed(2));

        }


    </script>

    @if(old('product'))
        <script>
            let itemId = $(".items").find(":selected").val();
            let row = $('.items').find(":selected").closest('tr');
            $(".items :selected").map(function(i, el) {
                console.log(parseInt($(el).val()))
                if(!isNaN(parseInt($(el).val())))
                {
                    $.ajax({
                        url: 'get-item/' + $(el).val(),
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            // Update the price input in the same row
                            row.find('.price').val(response.price);
                            // Optionally, you can calculate the amount based on qty and price
                            let qty = row.find('.quantity').val();
                            let amount = qty * response.price;
                            row.find('.total_amount').text(amount.toFixed(2));
                            // Update the total amount
                            updateTotalAmount();
                        }
                    });
                }

            });




        </script>
    @endif
    </body>
</html>
