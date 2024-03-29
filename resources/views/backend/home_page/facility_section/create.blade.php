@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl_style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Add Facility') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
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
        <a href="#">{{ __('Facility Section') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Add Facility') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-10">
              <div class="card-title">{{ __('Create Facility') }}</div>
            </div>

            <div class="col-lg-2">
              <a 
                class="btn btn-info btn-sm float-right d-inline-block"
                href="{{ route('admin.home_page.facility_section') . '?language=' . request()->input('language') }}"
              >
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
              <form 
                id="ajaxForm"
                action="{{ route('admin.home_page.facility_section.store_facility', ['language' => request()->input('language')]) }}"
                method="post"
              >
                @csrf
                <div class="form-group">
                  <label for="">{{ __('Icon*') }}</label>
                  <div class="btn-group d-block">
                    <button 
                      type="button" 
                      class="btn btn-primary iconpicker-component"
                    ><i class="fa fa-fw fa-heart"></i></button>
                    <button 
                      type="button" 
                      class="icp icp-dd btn btn-primary dropdown-toggle" 
                      data-selected="fa-car"
                      data-toggle="dropdown"
                    ></button>
                    <div class="dropdown-menu"></div>
                  </div>
                  <input type="hidden" id="inputIcon" name="facility_icon">
                  <p id="err_facility_icon" class="mt-1 mb-0 text-danger em"></p>
                  <div class="text-warning mt-2">
                    <small>{{ __('Click on the dropdown icon to select a icon.') }}</small>
                  </div>
                </div>

                <div class="form-group">
                  <label for="">{{ __('Facility Title*') }}</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="facility_title" 
                    placeholder="Enter Facility Title"
                  >
                  <p id="err_facility_title" class="mt-1 mb-0 text-danger em"></p>
                </div>

                <div class="form-group">
                  <label for="">{{ __('Facility Text*') }}</label>
                  <textarea 
                    class="form-control" 
                    name="facility_text" 
                    rows="5" 
                    cols="80"
                    placeholder="Enter Facility Text"
                  ></textarea>
                  <p id="err_facility_text" class="mt-1 mb-0 text-danger em"></p>
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
