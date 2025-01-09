<div class="form-group mb-3">
    <label class="control-label">{{ __('Title') }}</label>
    {!! Form::input('text', 'title', $content, ['class' => 'form-control', 'data-shortcode-attribute' => 'title', 'placeholder' => 'Our Mission']) !!}
</div>
<div class="form-group mb-3">
    <label class="control-label">{{ __('Description') }}</label>
    {!! Form::textarea('description', $content, ['class' => 'form-control', 'data-shortcode-attribute' => 'description']) !!}
</div>


