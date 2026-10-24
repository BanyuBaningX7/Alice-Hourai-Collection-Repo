@props(['status', 'label' => null])
<span class="badge b-{{ $status }}">{{ $label ?? ucfirst($status) }}</span>
