@extends('layouts.app')

@section('title', 'Create Category')

@section('content')

<style>
    .category-row-block {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .category-row-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #cbd5e1;
    }
    .category-row-badge {
        font-size: 13.5px;
        font-weight: 700;
        color: #0284c7;
    }
    .btn-remove-category {
        background: #ef4444;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
    }
    .btn-add-category {
        background: #0284c7;
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: bold;
        cursor: pointer;
    }
</style>

<div class="container">

    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 520px; margin: 0 auto 15px auto;">
        <h1 style="margin: 0;">Create Category</h1>
        <button type="button" class="btn-add-category" onclick="addCategoryRow()">+ Add Category</button>
    </div>

    @if ($errors->any())
        <div class="error-box" style="max-width: 520px; margin: 0 auto 15px auto;">
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
        style="max-width: 520px; margin: 0 auto;"
    >
        @csrf

        <div id="category-rows-container">
            {{-- Dynamic rows will be rendered here --}}
        </div>

        <button
            type="submit"
            class="primary-button"
            style="width: 100%; margin-top: 5px;"
        >
            Save Categories
        </button>

    </form>

    <div style="max-width: 520px; margin: 15px auto 0 auto;">
        <a
            class="back-link"
            href="{{ route('category.index') }}"
        >
            ← Back to Categories
        </a>
    </div>

</div>

<script>
let categoryCount = 0;

function addCategoryRow() {
    const container = document.getElementById('category-rows-container');
    const index = categoryCount++;

    const row = document.createElement('div');
    row.className = 'category-row-block';
    row.id = `category-block-${index}`;

    row.innerHTML = `
        <div class="category-row-top">
            <span class="category-row-badge">Category #${container.children.length + 1}</span>
            <button type="button" class="btn-remove-category" onclick="removeCategoryRow(${index})">✕ Remove</button>
        </div>

        <div class="form-group" style="margin-bottom: 12px;">
            <label for="name_${index}">Category Name</label>
            <input
                type="text"
                id="name_${index}"
                name="categories[${index}][name]"
                placeholder="Enter category name"
                required
            >
        </div>

        <div class="form-group" style="margin-bottom: 12px;">
            <label for="description_${index}">Description</label>
            <textarea
                id="description_${index}"
                name="categories[${index}][description]"
                placeholder="Enter category description"
                rows="2"
            ></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="status_${index}">Status</label>
            <select id="status_${index}" name="categories[${index}][status]">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
    `;

    container.appendChild(row);
    refreshLabels();
}

function removeCategoryRow(index) {
    const container = document.getElementById('category-rows-container');
    if (container.children.length <= 1) {
        alert("At least one category is required.");
        return;
    }

    const row = document.getElementById(`category-block-${index}`);
    if (row) row.remove();
    refreshLabels();
}

function refreshLabels() {
    const rows = document.querySelectorAll('.category-row-block');
    rows.forEach((row, idx) => {
        const badge = row.querySelector('.category-row-badge');
        if (badge) badge.innerText = `Category #${idx + 1}`;
    });
}

document.addEventListener('DOMContentLoaded', function () {
    addCategoryRow();
});
</script>

@endsection