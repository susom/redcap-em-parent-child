<?php

namespace Stanford\ParentChild;

/** @var \Stanford\ParentChild\ParentChild $module */

use \REDCap;

/**
 * this form is to search only top parent. but you need to set the search object using first event id
 */
?>

<div class="input-group md-form form-sm form-1 pl-0">
    <div class="input-group-prepend">
        <span class="input-group-text purple lighten-3" id="search-top-parent"><i class="fas fa-search text-white"
                                                                                  aria-hidden="true"></i></span>
    </div>
    <input class="form-control my-0 py-1" type="text" id="top-parent-field"
           placeholder="Search First Arm Instruments"
           aria-label="Search" required>
</div>