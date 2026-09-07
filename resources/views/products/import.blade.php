@extends('layouts.app')

@section('title', 'Import Products via Excel')

@section('content')

<style>
    .import-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        max-width: 680px;
        margin: 30px auto;
        padding: 35px 40px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        color: #1e293b;
    }

    .format-guide {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 25px;
        font-size: 13px;
        line-height: 1.6;
    }

    .file-dropzone {
        border: 2px dashed #94a3b8;
        border-radius: 6px;
        padding: 25px;
        text-align: center;
        background: #fafafa;
        margin-bottom: 20px;
    }
</style>

<div class="import-card">
    <h2 style="margin-top: 0; color: #0284c7;">Bulk Product Import via Excel</h2>
    <p style="font-size: 13.5px; color: #64748b; margin-bottom: 20px;">
        Upload a spreadsheet (.xlsx or .xls) to batch insert products and automatically assign or create categories.
    </p>

    @if (session('error'))
        <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="format-guide">
        <strong>Required Excel Column Headers (Row 1):</strong>
        <code style="display: block; background: #e2e8f0; padding: 7px; margin-top: 6px; border-radius: 4px; font-weight: bold;">
            name | category | qty | price | description | image
        </code>
        <small style="color: #64748b; display: block; margin-top: 8px;">
            • If the <strong>category</strong> does not exist, the system automatically creates it.<br>
            • For the <strong>image</strong> column, paste a public image web link (e.g. <code>https://.../photo.jpg</code>) or a filename located in <code>storage/app/public/products/</code>.
        </small>
    </div>

    <form action="{{ route('product.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="file-dropzone">
            <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required style="font-size: 14px;">
        </div>

        <button type="submit" class="primary-button" style="width: 100%; padding: 11px; font-size: 14px; font-weight: bold; background: #0284c7; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Upload & Process Excel
        </button>
    </form>

    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('product.index') }}" style="color: #64748b; font-size: 13.5px; text-decoration: none;">
            ← Back to Products List
        </a>
    </div>
</div>

@endsection