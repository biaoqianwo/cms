<?php

class CExcel
{
    private $__settings = null;
    private $__file_identity = null, $__cache_identity = null;

    private $__zip = null;
    private $__file_data = null, $__file_download = null;

    private $__record_count = 0, $__page = 0;

    public function create()
    {
        $this->__createSharedStrings();
        $this->__createSheetData();
        $this->__createExcel();
    }

    public function load($data)
    {
        $this->__record_count += count($data);
        $this->__page += 1;
        file_put_contents($this->__cache_identity . '.' . $this->__page . '.json', json_encode($data));
    }

    public function download($name = 'sheet.xlsx')
    {
        if (!file_exists($this->__cache_identity)) {
            throw new Exception('Failed to export the data, please try again');
        }
        header('Cache-Control: no-cache');
        header('Pragma: no-cache');
        header('Content-type: application/octet-stream');
        header('Accept-Ranges: bytes');
        header('Accept-Length: ' . filesize($this->__cache_identity));
        header("Content-Disposition:attachment;filename={$name}");
        $this->__file_download = fopen($this->__cache_identity, 'r');
        while (!feof($this->__file_download)) {
            echo fread($this->__file_download, 1024);
        }
        fclose($this->__file_download);
    }

    public function __construct($settings = array())
    {
        $this->__settings = array_merge(array(
            'height' => 20,
            'width' => 30
        ), $settings);

        $this->__createCacheFile();
    }

    private function __createExcel()
    {
        $this->__zip->addFile($this->__cache_identity . '.sharedStrings', 'xl/sharedStrings.xml');
        $this->__zip->addFile($this->__cache_identity . '.sheet1', 'xl/worksheets/sheet1.xml');
        $this->__zip->close();
    }

    private function __createCacheFile()
    {
        list($time, $date) = explode(' ', microtime());
        $this->__file_identity = $date . substr($time, 1) . sprintf('.%u', rand(1000, 9999));
        $this->__cache_identity = $this->__settings['cache'] . DIRECTORY_SEPARATOR . $this->__file_identity;
        $this->__zip = new ZipArchive();
        if (copy($this->__settings['template'], $this->__cache_identity) == false) {
            throw new Exception('Failed to create user templates');
        }
        if ($this->__zip->open($this->__cache_identity) == false) {
            throw new Exception('Failed to open user templates');
        }
    }

    //sheet xl sharedStrings.xml
    private function __createSharedStrings()
    {
        $this->__file_data = fopen($this->__cache_identity . '.sharedStrings', 'w');
        fwrite($this->__file_data,
            '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' . PHP_EOL .
            '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . PHP_EOL
        );

        foreach ($this->__settings['param'] as $row) {
            foreach ($row as $val) {
                fwrite($this->__file_data,
                    '	<si>' . PHP_EOL .
                    '		<t>' . $val . '</t>' . PHP_EOL .
                    '	</si>' . PHP_EOL
                );
            }
        }

        foreach ($this->__settings['column'] as $val) {
            fwrite($this->__file_data,
                '	<si>' . PHP_EOL .
                '		<t>' . $val . '</t>' . PHP_EOL .
                '	</si>' . PHP_EOL
            );
        }

        for ($page = 1; $page <= $this->__page; $page++) {
            $data = file_get_contents($this->__cache_identity . '.' . $this->__page . '.json');
            $data = json_decode($data, true);
            foreach ($data as $key => $row) {
                foreach ($row as $field => $val) {
                    fwrite($this->__file_data,
                        '	<si>' . PHP_EOL .
                        '		<t>' . $val . '</t>' . PHP_EOL .
                        '	</si>' . PHP_EOL
                    );
                }
            }
        }

        fwrite($this->__file_data, '</sst>');
        fclose($this->__file_data);
    }

    //sheet xl worksheets sheet1.xml
    private function __createSheetData()
    {
        $this->__file_data = fopen($this->__cache_identity . '.sheet1', 'w');
        fwrite($this->__file_data,
            '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' . PHP_EOL .
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac" mc:Ignorable="x14ac">' . PHP_EOL .
            '	<dimension ref="A1:' . $this->__getColumnName(64 + count($this->__settings['column'])) . ($this->__settings['row'] + $this->__record_count) . '" />' . PHP_EOL .
            '	<sheetFormatPr customHeight="1" defaultRowHeight="' . $this->__settings['height'] . '"  x14ac:dyDescent="0.25" />' . PHP_EOL
        );

        //写入列宽
        fwrite($this->__file_data, '	<cols>' . PHP_EOL);
        foreach ($this->__settings['column'] as $key => $val) {
            $key += 1;
            fwrite($this->__file_data, '		<col min="' . $key . '" max="' . $key . '" width="' . $this->__settings['width'] . '" customWidth="1"/>' . PHP_EOL);
        }
        fwrite($this->__file_data, '	</cols>' . PHP_EOL);

        //写入参数
        fwrite($this->__file_data, '	<sheetData>' . PHP_EOL);
        $index = 0;
        foreach ($this->__settings['param'] as $row => $columnValue) {
            fwrite($this->__file_data, '		<row r="' . $row . '">' . PHP_EOL);
            $s = 1;
            for ($columnIndex = 1, $columnCount = count($this->__settings['column']) + 1; $columnIndex < $columnCount; $columnIndex++) {
                $columnName = $this->__getColumnName(64 + $columnIndex) . $row;
                if (isset($this->__settings['param'][$row][$columnIndex])) {
                    if (count($this->__settings['param'][$row]) < 2) $s++;
                    fwrite($this->__file_data,
                        '		<c r="' . $columnName . '" t="s" s="' . $s . '">' . PHP_EOL .
                        '			<v>' . $index++ . '</v>' . PHP_EOL .
                        '		</c>' . PHP_EOL
                    );
                } else {
                    fwrite($this->__file_data, '		<c r="' . $columnName . '" s="' . $s . '"/>' . PHP_EOL);
                }
            }
            fwrite($this->__file_data, '		</row>' . PHP_EOL);
        }

        //写入数据
        for ($row = $this->__settings['row'], $index = count($this->__settings['param']) + 1; $row < $this->__record_count + $this->__settings['row'] + 1; $row++) {
            fwrite($this->__file_data, '		<row r="' . $row . '">' . PHP_EOL);
            $s = ($this->__settings['columnCenter'] == true && $row == $this->__settings['row']) ? ' s="2"' : '';
            foreach ($this->__settings['column'] as $key => $val) {
                $columnName = $this->__getColumnName(65 + $key) . $row;
                fwrite($this->__file_data,
                    '		<c r="' . $columnName . '" t="s"' . $s . '>' . PHP_EOL .
                    '			<v>' . $index++ . '</v>' . PHP_EOL .
                    '		</c>' . PHP_EOL
                );
            }
            fwrite($this->__file_data, '		</row>' . PHP_EOL);
        }
        fwrite($this->__file_data, '	</sheetData>' . PHP_EOL);

        //写入合并
        if (isset($this->__settings['merge']) && $this->__settings['merge']) {
            fwrite($this->__file_data, '	<mergeCells count="' . count($this->__settings['merge']) . '">' . PHP_EOL);
            foreach ($this->__settings['merge'] as $merge) {
                list($start, $end) = explode(':', $merge);
                list($start_column, $start_row) = explode(',', $start);
                list($end_column, $end_row) = explode(',', $end);
                $merge = $this->__getColumnName(64 + $start_column) . $start_row . ':' . $this->__getColumnName(64 + $end_column) . $end_row;
                fwrite($this->__file_data, '		<mergeCell ref="' . $merge . '" />' . PHP_EOL);
            }
            fwrite($this->__file_data, '	</mergeCells>' . PHP_EOL);
        }

        fwrite($this->__file_data, '</worksheet>');
        fclose($this->__file_data);
    }

    private function __getColumnName($column)
    {
        if ($column < 65 || $column > 766) {
            throw new Exception('Export column to 766');
        }
        $multiple = intval(($column - 65) / 26);

        $tens = $multiple + 64;
        $unit = $column - $multiple * 26;

        return ($tens > 64 ? chr($tens) : '') . chr($unit);
    }

    public function __destruct()
    {
        if (is_null($this->__cache_identity)) {
            return false;
        }

        if (stristr(php_uname(), 'Linux') != false) {
            exec("rm -rf {$this->__cache_identity}*");
        } else {
            exec("cmd /c del /f /s /q {$this->__cache_identity}*");
        }
    }
}
