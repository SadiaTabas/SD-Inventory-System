@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')

<div class="container">

 
<h1>Edit Category</h1>

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
    action="{{ route('category.update', $category) }}"
>

    @csrf
    @method('PUT')

    <div class="form-group">

        <label for="name">Category Name</label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $category->name) }}"
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
        >{{ old('description', $category->description) }}</textarea>

    </div>

    <div class="form-group">

        <label for="status">Status</label>

        <select id="status" name="status">

            <option
                value="1"
                {{ $category->status == 1 ? 'selected' : '' }}
            >
                Active
            </option>

            <option
                value="0"
                {{ $category->status == 0 ? 'selected' : '' }}
            >
                Inactive
            </option>

        </select>

    </div>

    <button
        type="submit"
        class="primary-button"
    >
        Update Category
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
