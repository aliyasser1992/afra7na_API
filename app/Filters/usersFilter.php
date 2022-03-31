<?php
namespace App\Filters;
use App\Filters\QueryFilter;
use Carbon\Carbon;
class usersFilter extends QueryFilter{

   

     public function country($eventsFilters){
          return $this->builder->where('country_id', $eventsFilters);
     }

     public function region($eventsFilters){
          return $this->builder->where('region_id', $eventsFilters);
     }


}