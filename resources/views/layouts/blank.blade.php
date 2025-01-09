<style>
@stack('ajaxCss')
</style>

@yield('content')

<script type="text/javascript">
$(document).ready(function() {
@stack('jsScripts')
});
@stack('jsFunc')
</script>
