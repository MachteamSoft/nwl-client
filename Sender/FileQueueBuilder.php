<?php

namespace Mach\Bundle\NwlBundle\Sender;

/**
 * Builds a FileQueue compatible source file
 *
 * @author Catalin Costache <catalin.costache@machteamsoft.ro>
 * @author Rares Vlasceanu <rares.vlasceanu@machteamsoft.ro>
 */
class FileQueueBuilder implements \Countable
{

    const RECORD_DELIMITER = "\x1e";
    const NEW_LINE_ENCODER = "\x1f";
    const DATA_DELIMITER = ",";
    const DATA_ENCLOSURE = "|";
    const TOTAL_COUNTER_MAX_LENGTH = 10;

    /**
     * @var integer
     */
    private $rows = 0;

    /**
     * @var resource
     */
    protected $handle;

    /**
     * @var array
     */
    protected $columns;

    /**
     * Open temp handle
     */
    public function __construct()
    {
        $this->handle = fopen(sprintf('php://temp/maxmemory:%d', 5 << 20), 'w+');
    }

    /**
     * Close handle
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    /**
     * @param \Doctrine_Query $sql
     */
    public function fromQuery(\Doctrine_Query $sql)
    {
        if (!$sql->getDqlPart('limit')) {
            $offset = 0;
            $limit = 1000;
            do {
                $runSql = clone($sql);
                $runSql->limit($limit);
                $runSql->offset($offset);
                $items = $runSql->fetchArray();
                $this->addResultSet($items);
                $offset += $limit;
            } while ($items);
        } else {
            $this->addResultSet($sql->fetchArray());
        }
    }

    /**
     * @param array $items
     * @return void
     */
    protected function addResultSet(array $items = array())
    {
        if (empty($items)) {
            return;
        }
        if (empty($this->columns)) {
            $this->setColumnNames(array_keys(current($items)));
        }
        foreach ($items as $item) {
            $this->addRow($item);
        }
    }

    /**
     * Set queue column headers
     *
     * @param array $columnNames
     * @throws \RuntimeException
     * @return void
     */
    public function setColumnNames(array $columnNames)
    {
        if (!empty($this->columns) && !empty($this->rows)) {
            throw new \RuntimeException('Could not set different header after writing data!');
        }

        ftruncate($this->handle, 0);
        $this->writeTotal(0, false);
        $this->write($columnNames);
        $this->columns = $columnNames;
    }

    /**
     * Add new row to queue
     *
     * @param array $rowData
     * @throws \RuntimeException
     * @return void
     */
    public function addRow(array $rowData)
    {
        $keys = array_keys($rowData);
        if (empty($this->columns)) {
            foreach ($keys as $key) {
                if (is_numeric($key)) {
                    throw new \RuntimeException('Please set header information first!');
                }
            }
            $this->setColumnNames($keys);
        }

        if (!empty($this->columns) && $keys !== $this->columns) {
            throw new \RuntimeException('The rowData array contains different keys from the registered header information');
        }
        $this->write($rowData);
        $this->rows++;
    }

    /**
     * Get all queue content
     *
     * Attention! Data could be large and reading it all at once might increase
     * memory consumption, or even cause a crash if memory limit is set too low
     *
     * @return string
     */
    public function getContents()
    {
        $this->writeTotal();

        return stream_get_contents($this->handle, -1, 0);
    }

    /**
     * Save data to file
     *
     * @param string $filePath
     * @throws \InvalidArgumentException
     * @return void
     */
    public function save($filePath = null)
    {
        if (empty($filePath)) {
            throw new \InvalidArgumentException('File path cannot be empty!');
        }

        $this->writeTotal();
        rewind($this->handle);

        $handle = fopen($filePath, 'w+');
        while (!feof($this->handle)) {
            fwrite($handle, fread($this->handle, 2048));
        }
        fclose($handle);
    }

    /**
     * Count existing items
     *
     * @return integer
     */
    public function count()
    {
        return $this->rows;
    }

    /**
     * Reset queue
     *
     * @param boolean $resetColumns
     */
    public function reset($resetColumns = false)
    {
        $this->rows = 0;
        if (!$resetColumns) {
            $this->setColumnNames($this->columns);
        } else {
            ftruncate($this->handle, 0);
            $this->columns = null;
        }
    }

    /**
     * @param integer $total
     * @param boolean $preserveCursorPosition
     */
    protected function writeTotal($total = null, $preserveCursorPosition = true)
    {
        if ($total === null) {
            $total = $this->rows;
        }

        $current = ftell($this->handle);
        fseek($this->handle, 0, SEEK_SET);
        fwrite($this->handle, str_pad(intval($total), self::TOTAL_COUNTER_MAX_LENGTH));
        if ($preserveCursorPosition) {
            fseek($this->handle, $current, SEEK_SET);
        }
    }

    /**
     * @param array $data
     */
    private function write(array $data = array())
    {
        fwrite($this->handle, $this->getCsvLine($data));
    }

    /**
     * As a workaround for problems caused by "fputcsv" function when dealing
     * with ASCII control chars (such as new line chars or escape char), each
     * data is sanitized and some chars are encoded/decoded before being written in CSV
     *
     * @see sanitizeData
     * @see encodeNewLine
     * @see decodeNewLine
     *
     * @param array $data
     * @return string
     */
    private function getCsvLine(array $data = array())
    {
        $buffer = fopen('php://temp', 'r+');
        fputcsv($buffer, $this->sanitizeData($data), self::DATA_DELIMITER, self::DATA_ENCLOSURE);
        rewind($buffer);
        $csv = $this->decodeNewLine(fgets($buffer));
        fclose($buffer);
        $csv[strlen($csv) - 1] = self::RECORD_DELIMITER;

        return $csv;
    }

    /**
     * Fix bug occuring when last char in string is escape ("\") or new line,
     * breaking string enclosure
     *
     * @param array $data
     * @throws \UnexpectedValueException
     * @return array
     */
    private function sanitizeData(array $data)
    {
        $pattern = '/'
                . '(?<!\\\\)'       # not preceded by a single backslash
                . '(?>\\\\\\\\)*'   # an even number of backslashes
                . '$/';

        foreach ($data as $key => &$item) {
            if (empty($item)) {
                continue;
            }
            if (!is_scalar($item)) {
                throw self::invalidValueTypeException($item, $key);
            }

            // fix for ending chars
            $last = substr($item, -1);
            switch ($last) {
                case '\\': {
                        if (!preg_match($pattern, $item)) {
                            $item .= '\\';
                        }
                    } break;
                case "\n":
                case "\r": {
                        $item = preg_replace('/(\n|\r|\s)+$/m', '', $item);
                    } break;
            }

            // encode all new line chars
            $item = $this->encodeNewLine($item);
        }

        return $data;
    }

    /**
     * @param string $string
     * @return string
     */
    private function encodeNewLine($string)
    {
        return str_replace(array("\r\n", "\r", "\n"), self::NEW_LINE_ENCODER, $string);
    }

    /**
     * @param string $string
     * @return string
     */
    private function decodeNewLine($string)
    {
        return str_replace(self::NEW_LINE_ENCODER, "\n", $string);
    }

    /**
     * @static
     * @param mixed $value
     * @param string $key
     * @return \UnexpectedValueException
     */
    protected static function invalidValueTypeException($value, $key)
    {
        $message = vsprintf('Expected value at key "%s" to be scalar, got "%s" (value: %s)', array(
            $key, gettype($value), print_r($value, true)
        ));

        return new \UnexpectedValueException($message);
    }

}
