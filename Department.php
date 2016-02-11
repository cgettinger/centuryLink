<?php
namespace centuryLink;
/**
 * Created by PhpStorm.
 * User: Carl Gettinger
 * Date: 2/8/2016
 * Time: 7:53 PM
 */
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
                    COUNT(CASE WHEN e.employeeType='manager' THEN 1 ELSE 0 END) as MANAGER,
                    COUNT(CASE WHEN e.employeeType='developer' THEN 1 ELSE 0 END) as DEVELOPER,
                    COUNT(CASE WHEN e.employeeType='qaTester' THEN 1 ELSE 0 END) as QA TESTER
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
