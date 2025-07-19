@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div id="modern-dashboard" class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <dashboard-layout :dashboard-data="dashboardData"></dashboard-layout>
</div>
@endsection

@push('scripts')
<script>
// Pass dashboard data to Vue component
window.dashboardData = @json($dashboardData ?? []);
</script>
@vite('resources/js/dashboard.js')
@endpush