<?php
namespace App\Http\Traits;

trait Searchable
{
    
    public function scopeSearch($query, $limit = 15, $query_input = 'query')
    {
        $query_string = request()->get($query_input);
        $words = explode(' ', $query_string);
        
        $searchables = isset($this->searchables) ? $this->searchables:[];
        foreach ($words as $word) {
            $query = $query->where(function ($q) use ($word, $searchables) {
                foreach($searchables as $key => $searchable)
                {
                    if( $key == 0 )
                        $q->where($searchable, 'like', "%{$word}%");
                    else
                        $q->orWhere($searchable, 'like', "%{$word}%");
                }
            });
        }

        

        if( is_null($limit) )
            return $query;

        return $query->take($limit);
    }


    public function scopeDataTable($query)
    {
        $page = 1;
        $rowsPerPage = 25;
        $sortBy = null;
        $sortDirection = 'asc';


        if( $pagination = request()->pagination )
        {
            $pagination = json_decode($pagination, true);
            $page = isset($pagination['page']) ?$pagination['page']:1;
            $rowsPerPage = isset($pagination['rowsPerPage']) ?$pagination['rowsPerPage']:25;
            $sortBy = isset($pagination['sortBy']) ? $pagination['sortBy']:null;
            
            if( isset($pagination['descending']) and $pagination['descending'] )
            {
                $sortDirection = 'desc';
            }
        }

        $skip = $rowsPerPage * ($page-1);
        

        $query = $query->search($rowsPerPage);
        
        $query_all = clone $query;
        
        $query = $query->skip($skip);

        if( $sortBy )
        {
            $query = $query->orderBy($sortBy, $sortDirection);
        }
        

        $data = $query->get();

        return [
            'items' => $data,
            'total' => $query_all->count()
        ];
    }
}