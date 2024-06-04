@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>User Link List</h1>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Text</th>
            <th>Link</th>
            <th>Click Count</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($links as $link)
            <tr>
                <td>{{ $link->text }}</td>
                <td><a href="#" class="update-redis" data-id='{{ $link->id}}' data-link="{{ $link->link }}" data-href="{{ $link->text }}">{{ $link->link }}</a></td>
                <td>{{ $link->click }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $links->links() }}
    </div>
</div>
{{----}}

@endsection
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('.update-redis').click(function(e) {
            e.preventDefault();
            console.log('href');
            var href = $(this).data('href');
            var link = $(this).data('link');
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('update.redis.value') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    link: link,
                    id: id,
                },
                success: function(response) {
                    if(response.success) {
                        alert('Value updated successfully!');
                        window.location.href = href;
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });
        });
    });
</script>
