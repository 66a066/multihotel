@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Add Info') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Home Page') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Intro Section') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Add Info') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-10">
              <div class="card-title">{{ __('Create Counter Info') }}</div>
            </div>

            <div class="col-lg-2">
              <a class="btn btn-info btn-sm float-right d-inline-block"
                href="{{ route('admin.home_page.intro_section') . '?language=' . $defaultLang->code }}">
                <span class="btn-label">
                  <i class="fas fa-backward"></i>
                </span>
                {{ __('Back') }}
              </a>
            </div>
          </div>
        </div>

        <div class="card-body pt-5 pb-5">
          <div class="row">
            <div class="col-lg-6 offset-lg-3">
              <form id="ajaxForm" action="{{ route('admin.home_page.intro_section.store_count_info') }}" method="post"
                class="create">
                @csrf
                <div class="form-group">
                  <label for="">{{ __('Language*') }}</label>
                  <select name="language_id" class="form-control">
                    <option selected disabled>{{ __('Select a Lanuage') }}</option>
                    @foreach ($langs as $lang)
                      <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                    @endforeach
                  </select>
                  <p id="err_language_id" class="mt-1 mb-0 text-danger em"></p>
                </div>
                <div class="form-group">
                  <label for="">{{ __('Icon*') }}</label>
                  <div class="btn-group d-block">
                    <button type="button" class="btn btn-primary iconpicker-component"><i
                        class="fa fa-fw fa-heart"></i></button>
                    <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle" data-selected="fa-car"
                      data-toggle="dropdown"></button>
                    <div class="dropdown-menu"></div>
                  </div>
                  <input type="hidden" id="inputIcon" name="icon">
                  <p id="err_icon" class="mt-1 mb-0 text-danger em"></p>
                  <div class="text-warning mt-2">
                    <small>{{ __('Click on the dropdown icon to select a icon.') }}</small>
                  </div>
                </div>

                <div class="form-group">
                  <label for="">{{ __('Title*') }}</label>
                  <input type="text" class="form-control" name="title" placeholder="Enter Counter Info Title">
                  <p id="err_title" class="mt-1 mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                  <label for="">{{ __('Amount*') }}</label>
                  <input type="number" class="form-control ltr" name="amount" placeholder="Enter Counter Info Amount">
                  <p id="err_amount" class="mt-1 mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                  <label for="">{{ __('Serial Number*') }}</label>
                  <input type="number" class="form-control ltr" name="serial_number"
                    placeholder="Enter Counter Info Serial Number">
                  <p id="err_serial_number" class="mt-1 mb-0 text-danger em"></p>
                  <p class="text-warning mt-2">
                    <small>{{ __('The higher the serial number is, the later the info will be shown.') }}</small>
                  </p>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" id="submitBtn" class="btn btn-success">
                {{ __('Save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
