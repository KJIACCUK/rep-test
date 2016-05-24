<?php

    require Yii::getPathOfAlias('application.vendor').'/autoload.php';

    class ImportBluestoneUsersFilter implements PHPExcel_Reader_IReadFilter
    {

        private $_startRow = 0;
        private $_endRow = 0;

        public function setRows($startRow, $chunkSize)
        {
            $this->_startRow = $startRow;
            $this->_endRow = $startRow + $chunkSize;
        }

        public function readCell($column, $row, $worksheetName = '')
        {
            if(($row >= $this->_startRow && $row < $this->_endRow))
            {
                if(in_array($column, range('A', 'E')))
                {
                    return true;
                }
            }

            return false;
        }

    }
    