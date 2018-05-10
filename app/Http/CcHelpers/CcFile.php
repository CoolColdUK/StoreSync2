<?php
namespace App\Http\CcHelpers;

class CcFile
{

    /**
     * convert csv file to associative array with first row as header
     *
     * @return array
     * @var string $file_path csv file path
     */
    public static function FileToAssocArray(string $file_path)
    {
        
        //try open file
        $handle = @file_path($sPath, "r");
        if ($handle === null) {
            return null;
        }

        //put file data to array
        $fields=array();
        $i=0;
        $data=array();
        while (($row = fgetcsv($handle, 4096)) !== false) {
            if (empty($fields)) {
                $fields = $row;
                continue;
            }
            foreach ($row as $k => $value) {
                $data[$i][$fields[$k]] = $value;
            }
            $i++;
        }

        if (!feof($handle)) {
            throw new ParseError("Exit before EOF");
        }
        fclose($handle);
        return $data;
    }

}
