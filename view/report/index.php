<!doctype html>
<html lang="en">
<head>
    <title>Relational REDCap Report</title>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"></link>

    <link rel="stylesheet" href="<?php echo $module->getUrl('assets/css/report.css') ?>">
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- DataTable Implementation -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>

    <style>
        body {
            word-wrap: break-word;
        }
    </style>
</head>
<body>

<div id="app" class="container">
    <input type="hidden" name="base-url" id="base-url"
           value="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . SERVER_NAME . APP_PATH_WEBROOT . 'DataExport/report_filter_ajax.php?pid=' . PROJECT_ID ?>">
    <input type="hidden" name="instrument-fields" id="instrument-fields"
           value="<?php echo $module->getUrl("ajax/fields.php") ?>">
    <input type="hidden" name="redcap_csrf_token" id="redcap_csrf_token" value="<?php echo System::getCsrfToken() ?>">
    <div class="row p-1">
        <h1>Relational REDCap Report</h1>
    </div>
    <div class="row p-1 d-none" id="show-filters">
        <button class="btn btn-link collapsed"><h3>Show Filters</h3></button>
    </div>
    <div id="filters-row" class="row p-1">
        <div class="col-lg-4">
            <?php
            require_once($module->getModulePath() . "view/report/instruments.php");
            ?>
        </div>
        <div class="col-lg-8">
            <!-- Correlated Report form -->
            <form name="correlated-report" id="correlated-report">
                <div class="row p-1">
                    <?php
                    require_once($module->getModulePath() . "view/report/filters.php");
                    ?>
                </div>
                <div class="row p-1">
                    <?php
                    require_once($module->getModulePath() . "view/report/fields.php");
                    ?>
                </div>
                <div class="row p-1">
                    <div class="col text-center">
                        <button type="submit" name="correlated-report-submit" class="btn btn-primary"
                                id="correlated-report-submit">Generate
                        </button>
                    </div>
                </div>
            </form>
            <!-- END Correlated Report form -->
        </div>
    </div>
    <div class="row p-1">
        <table id="report-result" class="display table table-striped table-bordered"
               cellspacing="0" width="100%"></table>
    </div>
</div>
<div class="loader"><!-- Place at bottom of page --></div>
<script src="<?php echo $module->getUrl('assets/js/report.js') ?>"></script>
</body>
</html>