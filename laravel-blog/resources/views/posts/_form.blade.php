@php
    $statusChecked = session()->hasOldInput()
        ? (bool) old('status')
        : (bool) $post->status;
@endphp

<form class="stack" method="post" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <label>
        Title
        <input type="text" name="title" value="{{ old('title', $post->title) }}" required>
    </label>
    @error('title')
        <p class="error">{{ $message }}</p>
    @enderror

    <label>
        Text
        <textarea name="content" rows="8" required>{{ old('content', $post->content) }}</textarea>
    </label>
    @error('content')
        <p class="error">{{ $message }}</p>
    @enderror

    <label>
        Category
        <select name="topic_id" required>
            <option value="">Choose a category</option>
            @foreach ($topics as $topic)
                <option value="{{ $topic->id }}" @selected((string) old('topic_id', $post->topic_id) === (string) $topic->id)>
                    {{ $topic->name }}
                </option>
            @endforeach
        </select>
    </label>
    @error('topic_id')
        <p class="error">{{ $message }}</p>
    @enderror

    <label>
        Image
        <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
    </label>
    @error('image')
        <p class="error">{{ $message }}</p>
    @enderror
    @if ($post->image)
        <img class="thumb" src="{{ asset('images/posts/'.$post->image) }}" alt="">
    @endif

    <label class="check">
        <input type="checkbox" name="status" value="1" @checked($statusChecked)>
        Published
    </label>

    <button class="button" type="submit">{{ $submit }}</button>
</form>
