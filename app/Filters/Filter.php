<?php
namespace Intranet\Filters;

interface Filter {

    public function exec(&$elements);
}