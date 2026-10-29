@extends('layouts.app')

@section('title', 'Tentang JDIH - Universitas Insan Mahardika')

@section('content')
    <section class="bg-blue-950 text-white">
        <div class="mx-auto max-w-4xl px-4 py-14 text-center">
            <p class="mb-2 text-sm font-medium uppercase tracking-wide text-blue-300">Tentang JDIH</p>
            <h1 class="text-3xl font-bold">Pertanyaan yang Sering Diajukan</h1>
            <p class="mx-auto mt-3 max-w-xl text-blue-200">
                Penjelasan seputar Jaringan Dokumentasi dan Informasi Hukum Universitas Insan Mahardika.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-12">
        <div class="space-y-3">
            @foreach ($faqs as $faq)
                <details class="group rounded-xl border border-slate-200 bg-white p-5 open:shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-slate-900">
                        {{ $faq['q'] }}
                        <svg class="h-4 w-4 shrink-0 text-slate-400 transition group-open:rotate-180" viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5"/></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>

        <div class="mt-10 rounded-xl border border-blue-100 bg-blue-50 p-6 text-center">
            <p class="font-semibold text-blue-900">Masih punya pertanyaan lain?</p>
            <p class="mt-1 text-sm text-blue-800">Hubungi pengelola JDIH di <a href="mailto:uimakademik@mahardika.ac.id" class="underline">uimakademik@mahardika.ac.id</a></p>
        </div>
    </section>
@endsection
