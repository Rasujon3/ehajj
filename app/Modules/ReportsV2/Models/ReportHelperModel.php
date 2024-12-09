<?php namespace App\Modules\ReportsV2\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class ReportHelperModel extends Model
{


    function list_view($id, $data, $report_title, $link = '')
    {
        $CI = get_instance();
        $dataTablePara = '';
        $showaction = false;
        $cols = array();
        $count = 1;
        if ($link) {
            $json_data = json_decode($link);
            if (!empty($json_data)) {
                foreach ($json_data as $jrow) {
                    if ($jrow->type == 'link') {
                        $showaction = true;
                    } else if ($jrow->type == 'dataTable') {
                        $dataTablePara = $jrow->properties;
                    } else if ($jrow->type == 'column') {
                        $cols[$jrow->ID]['caption'] = $jrow->caption;
                        $cols[$jrow->ID]['style'] = $jrow->style;
                    } else {
                        $showaction = true;
                    }
                }
            }
        }

        // echo '<pre>';print_r($cols);echo '</pre>';
        ?>
        <div class="graph_box">
            <h4>
                <?php echo $report_title; ?>
            </h4>
            <?php if (count($data) > 0) { ?>
                <table id="dbt_<?php echo $id; ?>" class="table table-responsive table-condensed" id="tblQInfo">
                    <thead>
                    <tr>
                        <?php
                        foreach ($data[0] as $key => $value) {
                            echo '<th';
                            if (isset($cols[$key]['style']))
                                echo ' style="' . $cols[$key]['style'] . '"';
                            echo '>';
                            echo isset($cols[$key]['caption']) ? $cols[$key]['caption'] : $key;
                            echo '</th>';
                        }
                        if ($showaction) {
                            echo '<th>Action</th>';
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach ($data as $row):
                        $rowdata = array();
                        if ($count >= $CI->config->item('MAX_DATA')) {
                            echo '<tfoot><tr><td colspan="5"><b>..Showing ' . $CI->config->item('MAX_DATA') . ' rows out of total ' . count($data) . '! Please export as CSV to show all data.</b></td></tr></tfoot>';
                            break;
                        }
                        $count++;
                        ?>
                        <tr>
                            <?php
                            foreach ($row as $key => $field_value):
                                //echo '<td>';
                                echo '<td';
                                if (isset($cols[$key]['style']))
                                    echo ' style="' . $cols[$key]['style'] . '"';
                                echo '>';
                                echo $field_value . '&nbsp;';
                                echo '</td>';
                                if ($link) {
                                    $rowdata[$key] = $field_value;
                                }
                            endforeach;

                            if ($showaction) {
                                echo '<td>';
                                foreach ($json_data as $jrow) {
                                    if ($jrow->type == 'link') {
                                        $rowdata['baseurl'] = base_url();
                                        echo '<a href="' . $this->ConvPara($jrow->url, $rowdata) . '">' . $jrow->caption . '</a>&nbsp;';
                                    } else if ($jrow->type == 'dataTable') {

                                    } else {
                                        print_r($jrow);
                                    }
                                }
                                echo '</td>';
                            }
                            ?>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                    </tbody>
                </table>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#dbt_<?php echo $id; ?>').dataTable({
                            "aaSorting": [],
                            "bPaginate": true,
                            "bLengthChange": false,
                            "bInfo": true,
                            "bAutoWidth": false,
                            'bFilter': false,
                            'iDisplayLength': 25
                            <?php
                            if (strlen($dataTablePara) > 0) {
                                echo ',' . $dataTablePara;
                            }
                            ?>
                        });
                    });</script>
                <?php
            } else {
                echo '<h4 style="text-align: center;color: gray">Data Not Found!</h4>';
            }
            ?>
        </div>

        <?php
    }

    function formatTDValue($cell, $maxwd = 50)
    {
        if ($cell) {
            if (filter_var($cell, FILTER_VALIDATE_URL)) {
                return "<a href='$cell' target='_blank'><i class='fa fa-link'></i> </a>";
            }
            elseif (strlen($cell) > $maxwd) {
                return '<span title="' . $cell . '">' . substr($cell, 0, $maxwd - 2) . '...</span>';
            } elseif (strlen($cell) > 12) {
                return $cell;
            } else if (is_float($cell)) {
                return '<span style="text-align:right;" title="' . $cell . '">' . number_format($cell, 2) . '</span>';
            } elseif (is_numeric($cell)) {
                if (substr($cell,0,1)=='0' or $cell<10000 or strlen($cell)>9) {
                    return $cell;
                } else {
                    return '<span style="text-align:right;" title="' . $cell . '">' . $cell . '</span>';
                }
            } else {
                return $cell;
            }
        } else {
            return '&nbsp;';
        }
    }

    function report_gen($id, $data, $report_title, $link = '', $heading = '')
    {
        $dataTablePara = '';
        $showaction = false;
        $cols = array();
        $count = 0;
        if ($link) {
            $json_data = json_decode($link);
            if (!empty($json_data)) {
                foreach ($json_data as $jrow) {
                    if ($jrow->type == 'link') {
                        $showaction = true;
                    } else if ($jrow->type == 'dataTable') {
                        $dataTablePara = $jrow->properties;
                    } else if ($jrow->type == 'column') {
                        $cols[$jrow->ID]['caption'] = $jrow->caption;
                        $cols[$jrow->ID]['style'] = $jrow->style;
                    } else {
                        $showaction = true;
                    }
                }
            }
        }
        ?>
        <div class="graph_box">
            <?php if ($heading) { ?>
                <div class="report_heading">
                    <div><?php echo $heading; ?></div>
                </div>
            <?php } ?>
            <?php if (count($data) > 1) { ?>
                <table id="report_data" class="display nowrap table-rpt-border table table-responsive table-condensed report_data_list">
                    <thead>
                    <tr>
                        <?php
                        foreach ($data[0] as $key => $value) {
                            echo '<th';
                            if (isset($cols[$key]['style']))
                                echo ' style="' . $cols[$key]['style'] . '"';
                            echo '>';
                            echo isset($cols[$key]['caption']) ? $cols[$key]['caption'] : $key;
                            echo '</th>';
                        }
                        if ($showaction) {
                            echo '<th>Action</th>';
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sl = 0;
                    foreach ($data as $row):
                        $rowdata = array();
                        if ($sl % 2 == 0) {
                            $row_bg_color = 'style="background-color:#FAFAFA"';
                        } else {
                            $row_bg_color = 'style=""';
                        }
                        if ($count >= 1500) {
                            echo '<tfoot><tr><td colspan="5"><b>Showing ' . 1500 . ' rows out of total ' . count($data) . '! Please export as CSV to show all data.</b></td></tr></tfoot>';
                            break;
                        }
                        $count++;
                        ?>
                        <tr <?php echo $row_bg_color; ?>>
                            <?php
                            foreach ($row as $key => $field_value):
                                //echo '<td>';
                                $td_align = is_numeric($field_value) ? 'text-align:center;' : '';
                                echo '<td';
                                if (isset($cols[$key]['style']))
                                    echo ' style="' . $cols[$key]['style'] . ';"';
                                echo '>';
                                echo $this->formatTDValue($field_value);
                                echo '</td>';
                                if ($link) {
                                    $rowdata[$key] = $field_value;
                                }
                            endforeach;
                            if ($showaction) {
                                echo '<td>';
                                foreach ($json_data as $jrow) {
                                    if ($jrow->type == 'link') {
                                        $rowdata['baseurl'] = base_url();
                                        echo '<a href="' . $this->ConvPara($jrow->url, $rowdata) . '">' . $jrow->caption . '</a>&nbsp;';
                                    } else if ($jrow->type == 'dataTable') {

                                    } else {
                                        print_r($jrow);
                                    }
                                }
                                echo '</td>';
                            }
                            ?>
                        </tr>
                        <?php
                        $sl++;
                    endforeach;
                    if ($count <= 1500) {
                        echo '<tfoot><tr><td colspan="5">Showing ' . $count . ' rows out of total ' . count($data) . ' Records</td></tr></tfoot>';
                    } ?>
                    </tbody>
                </table>

                <?php
            } elseif (count($data) === 1) {
                ?>
                <div class="col-sm-8">
                    <table id="report_data" class="table-rpt-border table table-responsive table-condensed table-bordered">
                        <?php
                        foreach ($data[0] as $label => $value) {
                            ?>
                            <tr>
                                <th width="40%"><?php echo $label; ?></th>
                                <td><?php echo $value; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
            } else {
                echo '<h4 style="text-align: center;color: gray">Data Not Found!</h4>';
            }
            ?>
        </div>

        <?php
        return $count;
    }

    function report_gen_exp($data, $report_title, $heading = '')
    {
        ?>
        <table border="1" rules="all" style="border:1px solid">
            <caption>
                <!--                <h2>--><?php //echo $report_title;
                ?><!--</h2>-->
                <?php if ($heading) { ?>
                    <div class="report_heading">
                        <div>
                            <b style="font-size: 16px;">
                                <?php echo $heading; ?><br>
                                <?php echo "Reaport's Title: " . $report_title . ''; ?>
                            </b>
                        </div>
                    </div>
                    <br/>
                <?php } ?>
            </caption>
            <?php if (count($data) > 0) { ?>
                <thead>
                <tr>
                    <?php
                    foreach ($data[0] as $key => $value) {
                        echo '<th>' . getFieldTitle($key) . '</th>';
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $row): ?>
                    <tr style=" border:1px solid">
                        <?php foreach ($row as $key => $field_value): ?>
                            <?php
                            if (is_numeric($field_value)) {
                                echo '<td style="text-align: center; vertical-align: middle; ">' . $field_value . '&nbsp; </td>';
                            } else {
                                echo '<td>' . $field_value . '&nbsp; </td>';
                            }
                            ?>

                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <?php
            } else {
                echo '<caption><h4>Data Not Found!</h4></caption>';
            }
            ?>
        </table>
        <?php
    }

    function ConvPara($sql, $data)
    {
////    $encryption = new Encryption();
//        $data['SC_ID'] = $_SESSION['SC_ID'];
//        $data['USER_ID'] = $_SESSION['USER_ID'];
        $CI = get_instance();
        $sql = ' ' . $sql;
        $start = strpos($sql, '{$');
        $i = 0;
        while ($start > 0) {
            $end = strpos($sql, '}', $start);
            if ($i++ > 20) {
                return $sql;
            }
            if ($end > $start) {
                $filed = substr($sql, $start + 2, $end - $start - 2);
                $var = array('', '');
                $var = explode(',', $filed);
                if (isset($var[1]) && $var[1] == 'ENC') {
//                $sql = substr($sql, 0, $start) . $data[$var[0]] . substr($sql, $end + 1);
                    $sql = substr($sql, 0, $start) . $CI->encryption->encode($data[$var[0]]) . substr($sql, $end + 1);
                } else {
                    $sql = substr($sql, 0, $start) . $data[$var[0]] . substr($sql, $end + 1);
                }
            }
            $start = strpos($sql, '{$');
        }
        return trim($sql);
    }

    function ConvParaEx($sql, $data, $sm = '{$', $em = '}', $optional = false)
    {
        $sql = ' ' . $sql;
        $start = strpos($sql, $sm);
        $i = 0;
        while ($start > 0) {
            if ($i++ > 20) {
                return $sql;
            }
            $end = strpos($sql, $em, $start);
            if ($end > $start) {
                $filed = substr($sql, $start + 2, $end - $start - 2);
                if (strtolower(substr($filed, 0, 8)) == 'optional') {
                    $optionalCond = $this->ConvParaEx(substr($filed, 9), $data, '[$', ']', true);
                    $sql = substr($sql, 0, $start) . $optionalCond . substr($sql, $end + 1);
                } else {
                    $inputData = $this->getData($filed, $data, substr($sql, 0, $start));
                    if ($optional && (($inputData == '') || ($inputData == "''"))) {
                        $sql = '';
                        break;
                    } else {
                        $sql = substr($sql, 0, $start) . $inputData . substr($sql, $end + 1);
                    }
                }
            }
            $start = strpos($sql, $sm);
        }
        return trim($sql);
    }

    function getData($filed, $data, $prefix = null)
    {
        $filedKey = explode('|', $filed);
        $val = trim($data[$filedKey[0]]);
        if (!is_numeric($val)) {
            if ($prefix) {
                $prefix = strtoupper(trim($prefix));
                if (substr($prefix, strlen($prefix) - 3) == 'IN(') {
                    $vals = explode(',', $val);
                    $val = '';
                    for ($i = 0; $i < count($vals); $i++) {
                        if (is_numeric($vals[$i])) {
                            $val .= (strlen($val) > 0 ? ',' : '') . $vals[$i];
                        } else {
                            $val .= (strlen($val) > 0 ? ',' : '') . "'" . $vals[$i] . "'";
                        }
                    }
                } elseif (!(substr($prefix, strlen($prefix) - 1) == "'" || substr($prefix, strlen($prefix) - 1) == "%")) {
                    $val = "'" . $val . "'";
                }
            }
        }
        if ($val == '') $val = "''";
        return $val;
    }


    function ConvCommonCondPara($sql, $data)
    {
        $sql = ' ' . $sql;
        $start = strpos($sql, '[$');
        $end = strpos($sql, ']', $start);
        if ($end > $start) {
            $filed = substr($sql, $start + 2, $end - $start - 2);
            if (isset($data[$filed]) && strlen($data[$filed]) > 0)
                $sql = substr($sql, 0, $start) . $data[$filed] . substr($sql, $end + 1);
            else
                $sql = '';
        }
        return trim($sql);
    }

    function ConvCommonCond($filed, $data)
    {
        $cond = array();
        //echo $filed . '<br>';
        $filed = ' ' . $filed;
        $filed = str_replace(' and ', ' AND ', $filed);
        $ands = explode(' AND ', $filed);
        //print_r($ands);
        foreach ($ands as $key => $value) {
            if (strlen(trim($value)) > 0) {
                $value = str_replace(' or ', ' OR ', $value);
                $ors = explode(' OR ', $value);
                if (count($ors) > 1) {
                    foreach ($ors as $key2 => $value2) {
                        if ($key2 == 0) {
                            if (strlen(trim($value2)) > 0)
                                $cond[] = ConvCommonCondAdd('AND', $value2, $data);
                        } else {
                            $cond[] = ConvCommonCondAdd('OR', $value2, $data);
                            //$cond[] = ' OR ' . $value2;
                        }
                    }
                } else {
                    $cond[] = ConvCommonCondAdd('AND', $value, $data);
                    //$cond[] = ' AND ' . $value;
                }
            }
        }
        $commoncond = '';
        foreach ($cond as $key2 => $value2) {
            $commoncond .= $value2;
        }
        return $commoncond;
    }

    function ConvCommonCondAdd($op, $value, $data)
    {
        $cond = ConvCommonCondPara($value, $data);
        if (strlen($cond) > 0)
            return ' ' . $op . ' ' . $cond;
        else
            return '';
    }

    function getSQLPara($sql, $s = '{$', $e = '}')
    {
        $sql = ' ' . $sql;
        $fileds = array();
        $start = strpos($sql, $s);
        $i = 0;
        while ($start > 0) {
            $end = strpos($sql, $e, $start);
            if ($i++ > 20) {
                return '';
            }
            if ($end > $start) {
                $filed = substr($sql, $start + 2, $end - $start - 2);
                if (strtolower(substr($filed, 0, 8)) == 'optional') {
                    $optionalfileds = $this->getSQLPara($filed, '[$', ']');
                    $fileds = array_merge($fileds, $optionalfileds);
                } else {
                    $var = array('', '');
                    $var = explode(',', $filed);
                    $f = explode('|', $var[0]);
                    if (!isset($fileds[$f[0]])) {
                        $fileds[$f[0]] = $filed; //$var[0];
                    } elseif (strlen($fileds[$f[0]]) < strlen($var[0])) {
                        $fileds[$f[0]] = $filed; //$var[0];
                    }
                }
                $sql = substr($sql, 0, $start) . '' . substr($sql, $end + 1);
            }
            $start = strpos($sql, $s);
//            print_r($fileds);
        }
//        dd($fileds);
        return $fileds;
    }

    function isSelected($data, $needle)
    {
        $pos = strpos(' ' . $data, $needle);
        if ($pos === false)
            return false;
        return true;
    }

    function report_gen_pdf($data, $report_title, $link = '')
    {
        $CI = get_instance();
        $html = '';
        $cols = array();
        $showaction = false;
        if ($link) {
            $json_data = json_decode($link);
            if (!empty($json_data)) {
                foreach ($json_data as $jrow) {
                    if ($jrow->type == 'link') {
                        $showaction = true;
                    } else if ($jrow->type == 'dataTable') {
                        $dataTablePara = $jrow->properties;
                    } else if ($jrow->type == 'column') {
                        $cols[$jrow->ID]['caption'] = $jrow->caption;
                        $cols[$jrow->ID]['style'] = $jrow->style;
                    } else {
                        $showaction = true;
                    }
                }
            }
        }
        $html .= '<style> td {border: 1px solid gray; font-size: 12px;} th{background-color: #c0c0c0; font-weight: bold; border: 1px solid black; font-size: 12px;} </style>';
        $html .= '<div class="graph_box">';
//    $html .= '<h4>';
//    $html .= $report_title;
//    $html .= '</h4>';
        if (count($data) > 0) {
            $html .= '<table border="1" rules="all">';
            $html .= '<thead>';
            $html .= '<tr>';

            foreach ($data[0] as $key => $value) {
                $html .= '<th ';
                if (isset($cols[$key]['style']))
                    $html .= ' style="' . $cols[$key]['style'] . '"';
                $html .= '>';
                if (isset($cols[$key]['caption'])) {
                    $html .= $cols[$key]['caption'];
                } else {
                    $html .= $key;
                }
                $html .= '</th>';
            }
            $html .= ' </tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $count = 1;
            foreach ($data as $row):
                $rowdata = array();
                if ($count >= $CI->config->item('MAX_DATA_PDF')) {
                    $html .= '<tfoot><tr><td colspan="5"><b>Showing ' . $CI->config->item('MAX_DATA_PDF') . ' rows out of total ' . count($data) . '! Please export as CSV to show all data.</b></td></tr></tfoot>';
                    break;
                }
                $count++;
                $html .= ' <tr>';
                foreach ($row as $key => $field_value):
                    $html .= '<td ';
                    if (isset($cols[$key]['style']))
                        $html .= ' style="' . $cols[$key]['style'] . '"';
                    $html .= '>';
                    $html .= $field_value . '&nbsp;';
                    $html .= '</td>';
                    if ($link) {
                        $rowdata[$key] = $field_value;
                    }
                endforeach;
                if ($showaction) {
                    $html .= '<td>';
                    foreach ($json_data as $jrow) {
                        if ($jrow->type == 'link') {
                            $rowdata['baseurl'] = base_url();
                            $html .= '<a href="' . $this->ConvPara($jrow->url, $rowdata) . '">' . $jrow->caption . '</a>&nbsp;';
                        } else if ($jrow->type == 'dataTable') {

                        } else {
                            print_r($jrow);
                        }
                    }
                    $html .= '</td>';
                }
                $html .= '</tr>';
            endforeach;
            $html .= '</tbody>';
            $html .= '</table>';
        } else {
            $html .= '<h4 style="text-align: center;color: gray">Data Not Found!</h4>';
        }
        $html .= '</div>';
        return $html;
    }

    function base64Decode($sql)
    {
        if (substr($sql, 0, 6) == 'select' || substr($sql, 0, 6) == 'SELECT') {
            return $sql;
        } else {
            return base64_decode($sql);
        }
    }

}