<?php

namespace p1\core\function;

interface Function2
{
    function apply($value);
}

interface Supplier
{
    function supply();
}

interface Consumer
{
    function consume($value): void;
}

interface Runnable
{
    function run(): void;
}