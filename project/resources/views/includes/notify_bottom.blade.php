<script>
@if (session('info'))

	notify_bottom('info', "{{ session('info') }}");

@elseif (session('status'))

	notify_bottom('info', "{{ session('status') }}");

@elseif (session('success'))

	notify_bottom('success', "{{ session('success') }}");

@elseif (session('warning'))

	notify_bottom('warning', "{{ session('warning') }}");

@elseif (session('error'))

	notify_bottom('danger', "{{ session('error') }}");
	
@endif
</script>