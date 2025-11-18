<form action="{{ route('products.destroy', $product) }}" method="POST" 
      class="d-inline" 
      onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">🗑️ Xóa</button>
</form>
