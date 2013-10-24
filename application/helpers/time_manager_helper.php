<?php

function has_errors($errors) {
    if ($errors != '') {
        return 'wrong';
    }
    return '';
}