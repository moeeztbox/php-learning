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
$skills = ["PHP", "Laravel", "MySQL", "Git", "GitHub"];

// ===============================
// FUNCTION
// ===============================
function greet($name)
{
    return "Hello, $name!";
}

// ===============================
// PARENT CLASS
// ===============================
class User
{
    public $name;
    protected $role;
    private $salary;

    public function __construct($name, $salary, $role)
    {
        $this->name = $name;
        $this->salary = $salary;
        $this->role = $role;
    }

    public function getUserInfo()
    {
        return "Name: {$this->name}, Role: {$this->role}";
    }

    public function getSalary()
    {
        return $this->salary;
    }
}

// ===============================
// CHILD CLASS (Inheritance)
// ===============================
class Developer extends User
{
    public $language;

    public function __construct($name, $salary, $role, $language)
    {
        parent::__construct($name, $salary, $role);
        $this->language = $language;
    }

    public function getDeveloperInfo()
    {
        return "{$this->name} is learning {$this->language}.";
    }
}

// ===============================
// OBJECT
// ===============================
$developer = new Developer(
    "Moeez Jamil",
    100000,
    "PHP Developer",
    "Laravel"
);

// ===============================
// IF ELSE
// ===============================
$status = ($age >= 18) ? "Adult" : "Minor";

// ===============================
// OUTPUT
// ===============================
echo "<h2>Day 1 - PHP OOP Practice</h2>";

echo greet($name);

echo "<hr>";

echo "<strong>Skills:</strong><br>";

foreach ($skills as $skill) {
    echo "- $skill <br>";
}

echo "<hr>";

echo "Age: $age <br>";
echo "Status: $status <br>";
echo "Developer: " . ($isDeveloper ? "Yes" : "No") . "<br><br>";

echo $developer->getUserInfo();
echo "<br>";

echo $developer->getDeveloperInfo();
echo "<br>";

echo "Salary: " . $developer->getSalary();