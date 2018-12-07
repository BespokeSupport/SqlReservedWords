<?php

namespace Example;

/**
 * Class Lead
 * @package Example
 *
 * Not OK in MySQL80
 */
class Group
{
    public $id; // OK in All
    public $user; // OK in MySQL - Not in PGSQL
}