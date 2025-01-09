<?php
use NaeemAwan\PredefinedLists\Models\PredefinedList;
use NaeemAwan\PredefinedLists\Models\PredefinedCategory;

if (!function_exists('currentPDLLevel')) {
    /**
     * return storage dir path
     */
    function currentPDLLevel($parent,$i)
    {
      // echo '>'.$i.'<';
      $model = PredefinedList::select('parent_id')->where('id',$parent)->first();
      if($model!=null){
        $i++;
        if($model->parent_id>0){
          $i = currentPDLLevel($model->parent_id,$i++);
        }
      }
      return $i;
    }

}

// newly added functions 2/21/2023
if (!function_exists('getEnableDisableIconWithLink')) {
  /**
  * return status for a given key with toggle link
  */
  function getEnableDisableIconWithLink($n,$route,$confrmMsg,$contnr)
  {
    return '<a href="'.url($route).'" class="act-confirmation" data-toggle="tooltip" title="" data-confirmationmsg="'.$confrmMsg.'" data-pjxcntr="'.$contnr.'">'.getStatusIconArr()[$n].'</a>';
  }
}
if (!function_exists('getStatusIconArr')) {
  /**
  * return status array
  */
  function getStatusIconArr()
  {
    return [
      '0'=>'<span class="label label-rounded label-warning"><i class="fas text-white fa-hourglass"></i></span>',
      '1'=>'<span class="label label-rounded label-success"><i class="fas text-white fa-check"></i></span>',
    ];
  }
}

if (!function_exists('PredefinedCategories')) {
  /**
  * return status array
  */
  function PredefinedCategories()
  {
    return PredefinedCategory::where('status',1)->get();
  }
}

if (!function_exists('childitems')) {
  /**
  * return status array
  */
  function childitems($id)
  {
    return PredefinedList::where('parent_id',$id)->get();
  }
}
if (!function_exists('lastitem')) {
  /**
  * return status array
  */
  function lastitem($id)
  {
    return PredefinedList::where(['parent_id'=>$id,'status'=>1])->latest()->first();
  }
}

if (!function_exists('getcategories')) {
  /**
  * return status array
  */
  function getcategories()
  {
    $options=PredefinedCategory::get();
    return $options->pluck('name','id')->toArray();
  }
}

if (!function_exists('getStatusArr')) {
  /**
  * return status array
  */
  function getStatusArr()
  {
    return [
      '1' => 'Enable',
      '0' => 'Disable',
    ];
  }
}

if (!function_exists('getSystemLanguages')) {
  /**
  * return status array
  */
  function getSystemLanguages()
  {
    return [
      'en' => 'en',
    ];
  }
}
if (!function_exists('getPackageDimensions')) {
  /**
  * return status array
  */
  function getPackageDimensions($weight)
  {
    $boxes = [
      ['weight' => 1, 'dimensions' => [337, 182, 100]],
      ['weight' => 2, 'dimensions' => [336, 320, 52]],
      ['weight' => 5, 'dimensions' => [337, 322, 180]],
      ['weight' => 10, 'dimensions' => [337, 322, 345]],
      ['weight' => 15, 'dimensions' => [417, 359, 369]],
      ['weight' => 20, 'dimensions' => [481, 404, 389]],
      ['weight' => 25, 'dimensions' => [541, 444, 409]],
    ];
    foreach ($boxes as $box) {
      if ($weight <= $box['weight']) {
          return $box['dimensions'];
      }
    }
    return [0,0,0];
  }
}

