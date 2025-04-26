<?php

namespace App\Helpers;

class ColumnChange
{
    public function __construct(
        public $column,
        public $from,
        public $to) {}
}
