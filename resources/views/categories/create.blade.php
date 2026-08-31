@extends('layouts.app')

@section('title', 'Create Category')

@section('content')

<div class="container">

 
<h1>Create Category</h1>

@if ($errors->any())

    <div class="error-box">

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

    </div>

@endif

<form
    class="form-container"
    method="POST"
    action="{{ route('category.store') }}"
>

    @csrf

    <div class="form-group">

        <label for="name">Category Name</label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name') }}"
            placeholder="Enter category name"
            required
        >

    </div>

    <div class="form-group">

        <label for="description">Description</label>

        <textarea
            id="description"
            name="description"
            placeholder="Enter category description"
        >{{ old('description') }}</textarea>

    </div>

    <button
        type="submit"
        class="primary-button"
    >
        Save Category
    </button>

</form>

<a
    class="back-link"
    href="{{ route('category.index') }}"
>
    ← Back to Categories
</a>
 

</div>

@endsection
