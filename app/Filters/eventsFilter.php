<?php
namespace App\Filters;
use App\Filters\QueryFilter;
use Carbon\Carbon;
class eventsFilter extends QueryFilter{

     public function title($eventsFilters){
          return $this->builder->where('title', 'like','%'.$eventsFilters.'%');
     }

     public function category($eventsFilters){
          return $this->builder->where('main_category_id', $eventsFilters);
     }

     public function country_id($eventsFilters){
          return $this->builder->where('country_id', $eventsFilters);
     }

     public function region_id($eventsFilters){
          return $this->builder->where('region_id', $eventsFilters);
     }

     public function user($eventsFilters){
          return $this->builder->where('user_id', $eventsFilters);
     }

     public function special($eventsFilters){
          return $this->builder->whereHas('special_event', function ($query) {
               $query->where('start_date', '<=', Carbon::now());
               $query->where('end_date', '>=', Carbon::now());
           });
     }


     public function special_for_admin($eventsFilters){
          return $this->builder->whereHas('special_event', function ($query) {
               $query->where('end_date', '>=', Carbon::now());
           });
     }

     

    
        


}