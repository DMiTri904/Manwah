@if($cart->items->count() > 0)
<div class="row mt-4">
    <div class="col-12 text-end">
        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-shopping-bag"></i> Tiến hành đặt hàng
        </a>
    </div>
</div>
@endif