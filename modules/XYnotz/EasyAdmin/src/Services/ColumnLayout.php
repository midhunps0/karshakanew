<?php
namespace Ynotz\EasyAdmin\Services;

class ColumnLayout extends LayoutElement
{
    public function __construct($width = "grow")
    {
        parent::__construct($width);
        $this->type = 'column';
    }
}
?>
