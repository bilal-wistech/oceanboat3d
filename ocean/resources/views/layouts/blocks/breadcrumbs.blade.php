@if (! empty($breadcrumbs))
<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
  <li class="breadcrumb-item text-muted">
    <a href="{{ url('/') }}" class="text-muted">
      {{__('app.general.home')}}
    </a>
  </li>
  @foreach ($breadcrumbs as $link => $label)

    @if (is_int($link) && ! is_int($label))
    <li class="breadcrumb-item active">
      {{ $label }}
    </li>
    @else
    <li class="breadcrumb-item text-muted">
      <a href="{{ url($link) }}" class="text-muted">
        {{ $label }}
      </a>
    </li>
    @endif
  @endforeach
</ul>
@endif
