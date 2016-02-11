<?php
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
        $jessica = array(Employee::TYPE_MANAGER, array($jamie, $bob, $becky, $jack, $billy));
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
        $carl = array(Employee::TYPE_MANAGER, array($jessica, $gop, $barak));

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

class Employee
{
    const TYPE_MANAGER = 'MANAGER';
    const TYPE_DEVELOPER = 'DEVELOPER';
    const TYPE_QA_TESTER = 'QA TESTER';
}

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

class Department
{
    public $departmentName;

    public function construct($departmentName)
    {
        $this->departmentName = $departmentName;
    }

    public function getEmployeeCounts($testing = array())
    {
        $test = true;
        //function makes a call to MySQL DB to get employee count.
        //when $testing is true we pass in a simple array to populate the returned $result.
        $sql = "SELECT
                    COUNT(CASE WHEN e.employeeType=manager THEN 1 END) as MANAGER,
                    COUNT(CASE WHEN e.employeeType=developer THEN 1 END) as DEVELOPER,
                    COUNT(CASE WHEN e.employeeType=qaTester THEN 1 END) as QA TESTER
                FROM employees e
                LEFT JOIN departments d ON d.departmentId = e.departmentId
                WHERE d.departmentName = ?";
        if (!$test && empty($testing)) {
            $stmt = DB::get_pdo()->prepare($sql);
            $stmt->execute(array($this->departmentName));
            $result = $stmt->fetchAll();
        } else {
            $result = array('MANAGER'=>$testing[0],
                'DEVELOPER'=>$testing[1],
                'QA TESTER'=>$testing[2]);
        }

        return $result;
    }
}
