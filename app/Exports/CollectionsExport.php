<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Exports;


use Maatwebsite\Excel\Concerns\FromArray;

class CollectionsExport implements FromArray
{
    protected $cols;

    public function __construct (array $cols)
    {
        $this->cols = $cols;
    }

    public function array (): array
    {
        return $this->cols;
    }
}