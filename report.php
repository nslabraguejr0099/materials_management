<?php
if ( $_SERVER['REQUEST_METHOD'] !== 'POST' && $_POST['mode'] !== 'report' )
    return;

require_once(__DIR__.'\modules\\'.$_POST['module'].'.php');

//  c
// lean data
$temp_data = $_POST;
$data = [];
$data['mode'] = $temp_data['mode']; unset($temp_data['mode']);
$data['module'] = $temp_data['module']; unset($temp_data['module']);
$data['search'] = $temp_data['search']; unset($temp_data['search']);
$data['filters'] = [];
foreach ( $temp_data as $key => $item ) {
    $keyName = str_replace('filter_', '', $key);
    $data['filters'][$keyName] = $item;
}

if ( $_POST['module'] == 'materials_locations' ) {
    $report = new Modules\MatLocModule;
    $report = $report->generate_report($data);
} else {
    return;
}
?>
<html>
<head>
<style>
    .tbl {
        width: 100%;
        border-collapse: collapse;
    }
    .tbl td, th {
        padding: 3px;
    }
    .tbl-display td, th {
        border: 1px solid black;
    }
</style>
</head>

<body>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: text-top; width: 49%;">
                <div style="margin-bottom: 10px; font-weight: bold;"><?php echo $report['title']; ?></div>
                <div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding-right: 10px; font-weight: bold; width: 1%; white-space: nowrap; vertical-align: text-top;">
                                Filters:
                            </td>
                            <td>
                                <table style="border-collapse: collapse;">
                                    <?php
                                        if ( count($report['filters']) > 0 ) {
                                            foreach ( $report['filters'] as $item ) {
                                                echo '<tr>';
                                                    echo '<td style="padding-right: 10px; font-weight: bold; width: 1%; white-space: nowrap;">'
                                                            .$item['title'].': '.
                                                        '</td>';
                                                    echo '<td>'.$item['text'].'</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<b>None</b>';
                                        }
                                    ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="vertical-align: text-top; width: 49%;">
                <div>
                    <b>PRINTED:</b>
                    <span style="float: right;"><?php echo date('F d, Y') ?></span>
                </div>
            </td>
        </tr>
    </table>

    <hr>

    <table class="tbl tbl-display">
        <thead>
            <tr>
                <?php
                    foreach ( $report['table_head'] as $item ) {
                        echo '<th>'.$item.'</th>';
                    }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ( $report['data'] as $tbl_data )
                {
                    echo '<tr>';
                    foreach ( $tbl_data as $tbl_dt ) {
                        echo '<td>'.$tbl_dt.'</td>';
                    }
                    echo '</tr>';
                }
            ?>
        </tbody>
    </table>
</body>


</html>