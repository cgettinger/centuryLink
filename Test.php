<?php
namespace centuryLink;

use \centuryLink\Allocation;
use \centuryLink\Employee;
use \centuryLink\Department;

function my_autoload($class)
{
    $file = '';
    $class = trim(str_replace('\\', '/', $class), '/');
    $parts = explode('/', $class);
    $class = array_pop($parts);
    $path = './';

    if (is_file( $path . $class .'.php')) {
        $file = $path . $class .'.php';
    }

    if ($file) {
        require_once $file;
    }
}
spl_autoload_register('CenturyLink\\my_autoload');

$testEmp1 = Test::testEmployeeSimple();
print_r($testEmp1);

$testEmp2 = Test::testEmployeeComplex();
print_r($testEmp2);

//$testArr===> values are employee counts. first number is managers, 2nd is developers, 3rd is qa testers.
$testArr = array(4,19,39);
$testDept = Test::testDepartment($testArr);
print_r($testDept);

/**
 * Created by PhpStorm.
 * User: Carl Gettinger
 * Date: 2/8/2016
 * Time: 7:53 PM
 */

class Test
{
    public static function testEmployeeSimple()
    {
        //declare Employees
        $jared = array(Employee::TYPE_DEVELOPER);
        $jimmy = array(Employee::TYPE_QA_TESTER);
        $billy = array(Employee::TYPE_MANAGER, array($jared, $jimmy));
        $carl = array(Employee::TYPE_MANAGER, array($billy));

        $allocation = new Allocation('employee');
        $carlAllocation = $allocation->getEmployeeAllocationAmount($carl);

        return $carlAllocation;
    }

    public static function testEmployeeComplex()
    {
        //declare Employees
        //for complex we will build the hierarchy 5 levels deep (5 managers)
        //COUNTS (managers = 5, developers = 7, qa testers = 4)
        //level 1
        $jared = array(Employee::TYPE_DEVELOPER);
        $janet = array(Employee::TYPE_QA_TESTER);
        $billy = array(Employee::TYPE_MANAGER, array($jared, $janet));
        //level 2
        $jamie = array(Employee::TYPE_DEVELOPER);
        $bob = array(Employee::TYPE_QA_TESTER);
        $becky = array(Employee::TYPE_DEVELOPER);
        $jack = array(Employee::TYPE_QA_TESTER);
        $jessica = array(Employee::TYPE_MANAGER, array($jamie, $bob, $becky, $jack));
        //level 3
        $donald = array(Employee::TYPE_DEVELOPER);
        $carson = array(Employee::TYPE_QA_TESTER);
        $ted = array(Employee::TYPE_DEVELOPER);
        $gop = array(Employee::TYPE_MANAGER, array($donald, $carson, $ted));
        //level 4
        $hilary = array(Employee::TYPE_DEVELOPER);
        $bernie = array(Employee::TYPE_DEVELOPER);
        $barak = array(Employee::TYPE_MANAGER, array($hilary, $bernie));
        //level 5
        $carl = array(Employee::TYPE_MANAGER, array($billy, $jessica, $gop, $barak));

        $allocation = new Allocation('employee');
        $employeeAllocation = $allocation->getEmployeeAllocationAmount($carl);

        return $employeeAllocation;
    }

    public static function testDepartment($testCounts)
    {
        $department = new Department('mens');
        $departmentEmployeeCounts = $department->getEmployeeCounts($testCounts);

        $allocation = new Allocation('department');
        $deptmentAllocation = $allocation->getDepartmentAllocationAmount($departmentEmployeeCounts);
        return $deptmentAllocation;
    }
}
