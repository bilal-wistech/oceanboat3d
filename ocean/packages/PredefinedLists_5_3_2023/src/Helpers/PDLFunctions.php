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

