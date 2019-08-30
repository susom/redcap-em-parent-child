<div class="card">
    <div class="card-header" id="<?php echo $key ?>-parent">
        <h5 class="mb-0">
            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#<?php echo $key ?>"
                    aria-expanded="true" aria-controls="<?php echo $key ?>">
                <?php echo $instrument ?>
            </button>
            <button type="button" class="mt-1 delete-criteria close get-children" data-event-id="<?php echo $event; ?>"
                    aria-label="Close"><i class="fas fa-plus"></i></button>
        </h5>
    </div>

    <div id="<?php echo $key ?>" class="collapse" aria-labelledby="<?php echo $key ?>-parent"
         data-parent="#accordionExample">
        <div class="card-body">
            <ul class="list-group instruments-fields connectedSortable">
                <?php
                $fields = REDCap::getFieldNames($key);
                foreach ($fields as $field) {
                    ?>
                    <li class="list-group-item" data-instrument="<?php echo $key ?>"
                        data-field="<?php echo $field ?>"
                        data-type="<?php echo REDCap::getFieldType($field) ?>"><?php echo $field ?>
                        <input type="hidden" name="limiter_name[]" value="<?php echo $field ?>">
                        <button type="button" class="mt-1  close include-field" data-field="<?php echo $field; ?>"
                                aria-label="Close"><i class="fas fa-plus"></i></button>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div>