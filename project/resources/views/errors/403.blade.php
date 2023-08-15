@extends('errors::layout')

@section('title', __('Forbidden Access'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Forbidden Access'))
