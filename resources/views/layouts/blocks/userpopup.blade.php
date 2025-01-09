@php
$memeberImage=Auth::user()->image;
if($memeberImage!=''){
  $memeberImage = getStorageFileUrl('images/'.$memeberImage);
}else{
  $memeberImage = asset('assets/media/users/default.jpg');
}
$jbRoleTitle = '';
$jobRole = getPredefinedItem(Auth::user()->jobrole_id);
if($jobRole!=null){
  $jobRoleTitle=$jobRole->lang->title;
}
@endphp
<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
  <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
    <h3 class="font-weight-bold m-0">{{__('common.user_profile')}}
      <small class="text-muted font-size-sm ml-2"></small>
    </h3>
    <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
      <i class="ki ki-close icon-xs text-muted"></i>
    </a>
  </div>
  <div class="offcanvas-content pr-5 mr-n5">
    <div class="d-flex align-items-center mt-5">
      <div class="symbol symbol-100 mr-5">
        <div class="symbol-label" style="background-image:url('{{$memeberImage}}')"></div>
      </div>
      <div class="d-flex flex-column">
        <a href="#" class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">{{ Auth::user() ? Auth::user()->name : '??' }}</a>
        <div class="navi mt-2">
          <a href="#" class="navi-item">
            <span class="navi-link p-0 pb-2">
              <span class="navi-icon mr-1">
                <span class="svg-icon svg-icon-lg svg-icon-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24" />
                      <path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000" />
                      <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5" />
                    </g>
                  </svg>
                </span>
              </span>
              <span class="navi-text text-muted text-hover-primary">{{ Auth::user() ? Auth::user()->email : '' }}</span>
            </span>
          </a>
          
        </div>
      </div>
    </div>
    <div class="separator separator-dashed mt-8 mb-5"></div>
    <div class="navi navi-spacer-x-0 p-0">
      <a href="{{url('update-profile')}}" class="navi-item">
        <div class="navi-link">
          <div class="symbol symbol-40 bg-light mr-3">
            <div class="symbol-label">
              <span class="svg-icon svg-icon-md svg-icon-success">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"></rect>
                    <path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000"></path>
                    <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5"></circle>
                  </g>
                </svg>
              </span>
            </div>
          </div>
          <div class="navi-text">
            <div class="font-weight-bold">{{__('common.my_profile')}}</div>
            <div class="text-muted">{{__('common.update_profile_msg')}}
              <span class="label label-light-primary label-inline font-weight-bold">{{__('common.update')}}</span>
            </div>
          </div>
        </div>
      </a>
      <a href="{{url('change-password')}}" class="navi-item">
        <div class="navi-link">
          <div class="symbol symbol-40 bg-light mr-3">
            <div class="symbol-label">
              <span class="svg-icon svg-icon-md svg-icon-primary">
                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                        <polygon id="path-1" points="0 0 24 0 24 24 0 24"></polygon>
                    </defs>
                    <g id="Stockholm-icons-/-General-/-Lock" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <mask id="mask-2" fill="white">
                            <use xlink:href="#path-1"></use>
                        </mask>
                        <g id="bound"></g>
                        <path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" id="Mask" fill="#000000"></path>
                    </g>
                </svg>
              </span>
            </div>
          </div>
          <div class="navi-text">
            <div class="font-weight-bold">{{__('common.security')}}</div>
            <div class="text-muted">{{__('common.update_password_msg')}}
              <span class="label label-light-success label-inline font-weight-bold">{{__('common.change')}}</span>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>
