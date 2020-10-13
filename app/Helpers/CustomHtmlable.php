<?php
namespace App\Helpers;

use Illuminate\Contracts\Support\Htmlable;

class CustomHtmlable implements Htmlable {
    protected $string = '';
    
    public function __construct($string) {
        $this->string = $string;
    }


    public function toHtml()
    {
        return $this->string;
    }
}
