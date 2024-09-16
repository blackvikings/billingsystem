<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::all();

        $response = '<option selected>Select Item</option>';
        foreach ($items as $item) {
            $response .= '<option value="' . $item->id . '">' . $item->name . '</option>';
        }

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getItem($id)
    {
        $item = Item::find($id);
        return response()->json($item);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request)
    {
        $order = new Order();
        $order->order_number = rand();
        $order->customer_name = $request->customer_name;
        $order->total_amount = $request->total_amount;
        $order->save();


        foreach ($request->product as $value) {
//            dd($value);
            $item = Item::find($value['items']);

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->id;
            $orderItem->quantity = $value['qty'];
            $orderItem->price = $item->price;
            $orderItem->total_price =  $orderItem->quantity * $orderItem->price;
            $orderItem->save();
        }

        return redirect()->back()->with('success', 'Order placed successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
    }
}
