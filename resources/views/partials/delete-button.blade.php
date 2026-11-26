<form action="{{ $action }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $message ?? 'Дійсно видалити запис?' }}')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">{{ $label ?? 'Видалити' }}</button>
</form>
