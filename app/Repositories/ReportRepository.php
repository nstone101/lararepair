<?php
namespace App\Repositories;

use Illuminate\Validation\ValidationException;

class ReportRepository
{

    public function __construct()
    {
    }


    public function getActiveStatuses($completed) {
		$q2 = \App\Status::where('completed', $completed)->get();        
        $status = array();
        foreach ($q2 as $row) {
            $status[] = $row->id;
        }
        return $status;
    }



    public function list_earnings($month, $year) {
        $data = $this->listPayments($month, $year);
        $number = array();
        for ($i = 1; $i <= 33; ++$i) {
            $number[$i] = 0;
        }
        for ($d = 0; $d <= count($data); ++$d) {
            $id = @date('j', strtotime($data[$d]['date']));
            $number[$id] = $number[$id] + @$data[$d]['grand_total'];
        }
        $number[32] = (int) $month;
        $number[33] = (int) $year;
        return $number;
    }
    public function list_closed_reparations($month, $year) {
        $completed = $this->getActiveStatuses(1);
        $active = $this->getActiveStatuses(0);
        $data = array();
        $data1 = array();

        $q = \App\Repair::orderBy('id', 'ASC');

        if (!empty($completed)) {
            $q->whereBetween('status_id', $completed);
        }else{
            $q->whereNotBetween('status_id', $active);
        } 

        $data = $q->selectRaw('DATE(closed_at) as date, grand_total')->where('closed_at', '!=', null)->get()->toArray();
        foreach ($data as $d) {
            if ($d['date']) {
                if ((date('m', strtotime($d['date'])) == $month) && (date('Y', strtotime($d['date'])) == $year)) {
                    $data1[] = $d;
                }
            }
        }
        return $data1;
    }

     public function listPayments($month, $year) {
        $data = array();
        $data1 = array();

        $q = \App\Payment::orderBy('id', 'ASC');

        $data = $q->selectRaw('DATE(created_at) as date, amount as grand_total')->get()->toArray();
        foreach ($data as $d) {
            if ($d['date']) {
                if ((date('m', strtotime($d['date'])) == $month) && (date('Y', strtotime($d['date'])) == $year)) {
                    $data1[] = $d;
                }
            }
        }

        return $data1;
    }

}