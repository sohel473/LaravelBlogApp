<x-layout>
    <div class="container py-md-5 container--narrow">
        <div class="d-flex justify-content-between">
            <h2>
                {{ $post->title }}
            </h2>
            <span class="pt-2">
                @can('update', $post)
                    <a href="/post/{{ $post->id }}/edit" class="text-primary mr-2" data-toggle="tooltip"
                        data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                @endcan

                @can('delete', $post)
                    <form class="delete-post-form d-inline" action="/post/{{ $post->id }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="delete-post-button text-danger" data-toggle="tooltip" data-placement="top"
                            title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                @endcan
            </span>
        </div>

        <p class="text-muted small mb-4">
            <a href="#"><img class="avatar-tiny" src={{ auth()->user()->avatar }} /></a>
            Posted by <a href="/profile/{{ $post->user->id }}">
                {{ $post->user->username }}
            </a> on {{ $post->created_at->format('n/j/Y') }}

        </p>

        <div class="body-content">
            {{ $post->body }}
        </div>
    </div>
</x-layout>
