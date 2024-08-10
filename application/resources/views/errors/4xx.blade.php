@extends('errors.template-errors')

@section('title', __('Client Error'))
@section('code', '400')
@section('message', __($exception->getMessage() ?: 'Client Error'))
