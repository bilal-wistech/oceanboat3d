@php
$subHeading = isset(app()->view->getSections()['pageSubHeading']) ?? '';
if($subHeading=='')$subHeading = app()->view->getSections()['title'] ?? '';

@endphp
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
  <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
    <div class="d-flex align-items-center flex-wrap mr-1">
      <div class="d-flex align-items-baseline flex-wrap mr-5">
        <h5 class="text-dark font-weight-bold my-1 mr-5">
          {{ $subHeading }}
        </h5>
        @yield('breadcrumbs')
      </div>
    </div>

    @if(isset($btnsList))

    <div class="d-flex align-items-center">
      @foreach ($btnsList as $btnItm)
      @if(isset($btnItm['subopts']) && $btnItm['subopts']!=null)
        <div class="dropdown dropdown-inline" data-toggle="tooltip" data-placement="top" data-original-title="{{__('common.switch_view')}}">
          <a href="javascript:;" class="btn btn-light-primary font-weight-bolder btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            List <i class="fas fa-angle-down"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-md dropdown-menu-right p-0 m-0">
            <ul class="navi navi-hover">
            @foreach($btnItm['subopts'] as $subOption)

              <li class="navi-item">
                <a href="{{$subOption['link']!='' ? url($subOption['link']) : 'javascript:;'}}" class="navi-link">
                  <span class="navi-text">
                    {{$subOption['label']}}
                  </span>
                </a>
              </li>
            @endforeach
            </ul>
          </div>
        </div>
      @else
      <a href="{{$btnItm['link']!='' ? url($btnItm['link']) : 'javascript:;'}}" class="btn btn-icon btn-{{$btnItm['class']}} btn-xs ml-2" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-{{$btnItm['icon']}}"></i> {{$btnItm['label']}}
      </a>
      @endif
      @endforeach
    </div>
    @endif
  </div>
</div>
