@extends('errors.template-errors')

@section('title', __('Internal Server Error'))
@section('code', '500')
@section('message', __($exception->getMessage() ?: 'Internal Server Error'))
