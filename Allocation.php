<?php
namespace centuryLink;
/**
 * Created by PhpStorm.
 * User: Carl Gettinger
 * Date: 2/8/2016
 * Time: 7:52 PM
 */
class Allocation
{
    public $allocationAmounts = array('MANAGER'=>300,
        'DEVELOPER'=>1000,
        'QA TESTER'=>500);

    public function getEmployeeAllocationAmount($employees)
    {
        //recursive function
        //recieves a multi-deminsional array of employees
        $total = 0;
        $total += $this->allocationAmounts[ $employees[0] ];
        //$employees[1] will only be set if $employees[0] == 'MANAGER'
        if ($employees[1]) {
            foreach ($employees[1] as $e) {
                //recursive call to get next level of hierarchy
                $total += $this->getEmployeeAllocationAmount($e);
            }
        }
        return $total;
    }

    public function getDepartmentAllocationAmount($counts)
    {
        $total = 0;

        foreach ($counts as $key=>$value) {
            $total += $this->allocationAmounts[ $key ] * $value;
        }
        return $total;
    }

}
