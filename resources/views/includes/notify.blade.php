<script>
@if (session('info'))

	notify_bar('info', "{{ session('info') }}");

@elseif (session('status'))

	notify_bar('info', "{{ session('status') }}");

@elseif (session('success'))

	notify_bar('success', "{{ session('success') }}");

@elseif (session('warning'))

	notify_bar('warning', "{{ session('warning') }}");

@elseif (session('error'))

	notify_bar('danger', "{{ session('error') }}");

@endif
</script>