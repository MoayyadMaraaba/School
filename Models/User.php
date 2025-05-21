<?php

class User
{
    private $id;
    private $name;
    private $email;
    private $age;
    private $gender;
    private $role;
    private $password;
    private $date;
    private $classId;

    // Constructor
    public function __construct($id, $name, $email, $age, $gender, $role, $password, $date, $classId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->age = $age;
        $this->gender = $gender;
        $this->role = $role;
        $this->password = $password;
        $this->date = $date;
        $this->classId = $classId;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getClassId()
    {
        return $this->classId;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setClassId($classId)
    {
        $this->classId = $classId;
    }
}
?>