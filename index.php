<?php

// ===============================
// VARIABLES
// ===============================
$name = "Moeez";
$age = 24;
$isDeveloper = true;

// ===============================
// ARRAY
// ===============================
$skills = ["PHP", "Laravel", "MySQL", "Git"];

// ===============================
// FUNCTION
// ===============================
function greet($name)
{
    return "Hello, $name!";
}

// ===============================
// CLASS
// ===============================
class User
{
    // Properties
    public $name;
    private $salary;
    protected $role;

    // Constructor
    public function __construct($name, $salary, $role)
    {
        $this->name = $name;
        $this->salary = $salary;
        $this->role = $role;
    }

    // Public Method
    public function getUserInfo()
    {
        return "Name: {$this->name}, Role: {$this->role}";
    }

    // Getter
    public function getSalary()
    {
        return $this->salary;
    }
}

// ===============================
// OBJECT
// ===============================
$user = new User("Moeez", 100000, "Developer");

// ===============================
// IF ELSE
// ===============================
if ($age >= 18) {
    $status = "Adult";
} else {
    $status = "Minor";
}

// ===============================
// LOOP
// ===============================
echo "<h2>Skills</h2>";

foreach ($skills as $skill) {
    echo $skill . "<br>";
}

// ===============================
// OUTPUT
// ===============================
echo "<hr>";

echo greet($name);

echo "<br><br>";

echo "Age: $age <br>";

echo "Status: $status <br>";

echo "Developer: " . ($isDeveloper ? "Yes" : "No") . "<br>";

echo "<br>";

echo $user->getUserInfo();

echo "<br>";

echo "Salary: " . $user->getSalary();