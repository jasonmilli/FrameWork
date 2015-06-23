<?php
function fib($n) {
    if ($n == 1) {
        return 0;
    } else if ($n == 2) {
        return 1;
    } else {
        return fib($n - 1) + fib($n - 2);
    }
}
for ($i = 1; $i < $argv[1]; $i++) {
    echo "$i: ".fib($i)."\n";
}
