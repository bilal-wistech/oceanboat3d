<?php
namespace NaeemAwan\PredefinedLists\Observers;

use NaeemAwan\PredefinedLists\Models\PredefinedList;
use NaeemAwan\PredefinedLists\Models\PredefinedListDetail;

class PredefinedListObserver
{
/**
* Handle the PredefinedList "created" event.
*
* @param  \NaeemAwan\PredefinedLists\Models\PredefinedList  $predefinedList
* @return void
*/
public function created(PredefinedList $predefinedList)
{
  if ($predefinedList->parent_id==0) {
    $list_details=new PredefinedListDetail;
    $list_details->category_id=request()->input('category_id');
    $list_details->details=request()->input('details');
    $list_details->standard_options=request()->input('standard_options');
    $list_details->list_id=$predefinedList->id;
    $list_details->images=request()->input('images');
    $list_details->url=request()->input('url');
    $list_details->save();
  }
}

/**
* Handle the PredefinedList "updated" event.
*
* @param  \App\Models\PredefinedList  $predefinedList
* @return void
*/
public function updated(PredefinedList $predefinedList)
{
// dd($predefinedList);

  if ($predefinedList->parent_id==0) {
    $list_details=PredefinedListDetail::where('list_id',$predefinedList->id)->first();
    if($list_details){
      $list_details->category_id=request()->input('category_id');
      $list_details->details=request()->input('details');
      $list_details->standard_options=request()->input('standard_options');
      $list_details->images=request()->input('images');
      $list_details->url=request()->input('url');
      $list_details->save();    
    }
    
  }

  
}

/**
* Handle the PredefinedList "deleted" event.
*
* @param  \App\Models\PredefinedList  $predefinedList
* @return void
*/
public function deleted(PredefinedList $predefinedList)
{
//
}

/**
* Handle the PredefinedList "restored" event.
*
* @param  \App\Models\PredefinedList  $predefinedList
* @return void
*/
public function restored(PredefinedList $predefinedList)
{
//
}

/**
* Handle the PredefinedList "force deleted" event.
*
* @param  \App\Models\PredefinedList  $predefinedList
* @return void
*/
public function forceDeleted(PredefinedList $predefinedList)
{
//
}
}
