<?php
namespace App\Helpers;

use App\Exports\DataTableExport;
use Maatwebsite\Excel\Facades\Excel;
class Datatable {
    protected $query;
    protected $page = 1;
    protected $rowsPerPage = 25;
    protected $sortBy = null;
    protected $sortDirection = 'asc';
    protected $skip;
    protected $searchables;
    protected $filters;
    protected $custom_columns;
    public function __construct()
    {
        $this->filters = collect();
        $this->custom_columns = collect();
    }
    public function search($limit = 15, $query_input = 'query')
    {
        $words = explode(' ', request()->get($query_input));
        $searchables = isset($this->searchables) ? $this->searchables:[];
        
        foreach ($words as $word) {
            if( empty($word) )
                continue;
            $this->query = $this->query->where(function ($q) use ($word, $searchables) {
                foreach($searchables as $key => $searchable)
                {
                    if( $key == 0 )
                        $q->where($searchable, 'like', "%{$word}%");
                    else
                        $q->orWhere($searchable, 'like', "%{$word}%");
                }
            });
        }
        
        foreach(request()->get('headers') as $header)
        {
            $header = json_decode($header);
            if( isset($header->has_filter) && $header->has_filter == true  && !empty($header->filter) && $filter = $this->filters->where('field', $header->value)->first() )
            {
                $this->query = $filter['callback']($this->query, $header->filter);
            }
        }
    }
    public function configFilter($field, $callback)
    {
        $this->filters->push(compact('field', 'callback'));
        return $this;
    }
    public function configColumn($field, $callback)
    {
        $this->custom_columns->push(compact('field', 'callback'));
        return $this;
    }
    public function query($query)
    {
        if( $pagination = request()->pagination )
        {
            $pagination = json_decode($pagination, true);
            $this->page = isset($pagination['page']) ? $pagination['page']:1;
            $this->itemsPerPage = isset($pagination['itemsPerPage']) ?$pagination['itemsPerPage']:25;
            $this->skip = ($this->page-1) * $this->itemsPerPage;
            $this->sortBy = isset($pagination['sortBy']) &&  sizeof($pagination['sortBy']) > 0? $pagination['sortBy'][0] : null;
            $this->sortDesc = isset($pagination['sortDesc']) &&  sizeof($pagination['sortDesc']) > 0 ? $pagination['sortDesc'][0] : null;
            
            if( $this->sortDesc )
            {
                $this->sortDirection = 'desc';
            }else{
                $this->sortDirection = 'asc';
            }
        }
        
        $this->query = $query;
        
        return $this;
    }
    public function setFilters()
    {
        $args = func_get_args();
        $this->searchables = $args;
        return $this;
    }
    public function get()
    {
        $this->search();
        
        $query_all = clone $this->query;
        if( !request()->excel and $this->itemsPerPage != -1 )
            $this->query = $this->query->take($this->itemsPerPage)->skip($this->skip);
        
        if( $this->sortBy )
        {
            $this->query = $this->query->orderBy($this->sortBy, $this->sortDirection);
        }
        
        
        $data = $this->query->get();
        $data = $data->map(function($item){
            foreach($this->custom_columns as $column)
            {
                if( isset($item->{$column['field']}) )
                {
                    $item->{$column['field']} = $column['callback']($item->{$column['field']}, $item);
                }
            }
            return $item;
        });
        
        if( request()->excel )
        {
            $export = new DataTableExport($data);
            return Excel::download($export, 'report.xlsx');
        }
        return [
            'items' => $data,
            'total' => $query_all->count()
        ];
    }
}
