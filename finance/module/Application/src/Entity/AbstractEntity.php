<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:11 PM
 */

namespace Application\Entity;

abstract class AbstractEntityEntity
{
    /**
     * @param object $inputDto
     */
    public function __construct($inputDto)
    {
        if ($inputDto) {
            foreach ($inputDto as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
    }
}