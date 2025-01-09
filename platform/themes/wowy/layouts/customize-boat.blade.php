{!! Theme::partial('header') !!}

<main class="main" id="main-section">
    @if (Theme::get('hasBreadcrumb', true))
        {!! Theme::partial('breadcrumb') !!}
    @endif

    <section class="">
        <div class="">
            {!! Theme::content() !!}
        </div>
    </section>
</main>

{!! Theme::partial('boat-footer') !!}


<script>
  $(document).ready(function() {
    $('#submit-form input[type="radio"]').change(function() {
        if ($(this).is(':checked')) {          
          var boatId = this.value;  // Replace with your dynamic boat ID

        $.ajax({
            url: "{{ route('public.customize-boat.add-view', ['id' => ':id', 'type' => 'option']) }}".replace(':id', boatId),
            method: 'GET',  // Adjust the HTTP method if necessary
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Add CSRF token for Laravel
            },
            success: function(response) {
               
            },
            error: function(xhr, status, error) {
                
            }
        });
          
            
        }
    });
});
  </script>