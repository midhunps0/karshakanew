<?php
namespace Ynotz\EasyAdmin\Services;

class RowLayout extends LayoutElement
{
    public function __construct($width = 'grow')
    {
        parent::__construct($width);
        $this->type = 'row';
    }
}
?>
