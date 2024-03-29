<?php
include_once 'db.php';
class User extends DB
{
    //private $username,$password;
 
    public function __construct()
    {
        parent::__construct();
    }
    public function userExistForLogin($u,$p)
    {
        
        $query=$this->connect()->prepare('SELECT * FROM users WHERE username=:u AND password=:p');
        $query->execute(['u'=> $u , 'p' => md5($p)]);
        if($query->rowCount())
            return true;
        return false;
    }
    public function userExist($u)
    { 
        $query=$this->connect()->prepare('SELECT id FROM users WHERE username=:u');
        $query->execute(['u'=> $u]);
        if($query->rowCount())
            return true;
        return false;
    }
    public function userExistPassword($u,$p)
    { 
        $query=$this->connect()->prepare('SELECT id FROM users WHERE username=:u AND password=:p');
        $query->execute(['u'=> $u, ":p"=> md5($p)]);
        if($query->rowCount())
            return true;
        return false;
    }
    public function getUserIDByName($username)
    {
        $conn=$this->connect();
        $sql="SELECT id FROM users 
                WHERE username=:un";
        $query=$conn->prepare($sql);
        $query->execute([":un"=> $username]);
        if($query->rowCount()> 0)    
            return $query->fetchColumn();
        return false;
    }
    public function setUserSession($username,$usernametelegram=null)
    {
        $this->setDataSession(
            array(
                    "user"=>$username,
                    "usernametelegram"=>$usernametelegram,
                    "role"=>$this->getUserRoleByUserName($username),
                    "validation_type"=>$this->getUserValidationTypeByUserName($username),
                    "validation_data"=>$this->getUserValidationByUserName($username),
                    "id"=> $this->getUserIDByName($username)
                )
            );
    }
    public function validationExist($validation_data)
    {
        $sql="SELECT id FROM user_validation 
            WHERE data=:d";
        $query=$this->connect()->prepare($sql);
        $query->execute([':d'=> $validation_data]);
        return $query->rowCount()> 0;
    }
    public function getUserNameTelegram($u)
    {
        $sql="SELECT user_telegram.username FROM user_telegram 
            JOIN users ON user_telegram.user_id=users.id 
            WHERE users.username=:u";
        $query=$this->connect()->prepare($sql);
        $query->execute([':u'=> $u]);
        if($query->rowCount())
            return $query->fetchColumn();
        return null;
    }
    
    public function closeSession()
    {
        session_unset();
        session_destroy();
    }
    public function updateUserName($username,$newuername)
    {
        $sql="UPDATE users SET username=:nun WHERE username=:un";
        $query=$this->connect()->prepare($sql);
        return $query->execute(['nun'=> $newuername, "un"=> $username]);
    }
    public function updatePassWUser($username,$password)
    {
        $sql="UPDATE users SET password=:p WHERE username=:un";
        $query=$this->connect()->prepare($sql);
        return $query->execute([':un'=> $username, ":p"=> md5($password)]);
    }
    public function getUserRole()
    {
        $username=$this->getDataSession("user");
        $sql="SELECT role FROM users WHERE username=:un";
        $query=$this->connect()->prepare($sql);
        $query->execute([':un'=> $username]);
        if( $query->rowCount() > 0)
            return $query->fetch(PDO::FETCH_ASSOC)["role"];
    }
    public function registerUser($username,$password,$type,$validation_data)
    {
        $conn=$this->connect();
        $sql="INSERT INTO users (username,password) 
                VALUES (:un,:pass)";
        $query=$conn->prepare($sql);
        if($query->execute([":un"=> $username ,":pass"=> md5($password)]))
            {
                $id=$this->getUserIDByName($username);
                if($id!==false)
                {
                    $sql="INSERT INTO user_validation (type,data,user_id) 
                            VALUES (:ty,:d,:ui)";
                    $query=$conn->prepare($sql);
                    if($query->execute([":ty"=> $type ,":d"=> $validation_data,":ui"=>$id]))
                        return true;
                }
                return true;
            }
        return false;
    }
    public function getUserRoleByUserName($username)
    {
        $sql="SELECT role FROM users WHERE username=:un";
        $query=$this->connect()->prepare($sql);
        $query->execute([':un'=> $username]);
        if( $query->rowCount() > 0)
            return $query->fetch(PDO::FETCH_ASSOC)["role"];
    }
    public function getUserValidationTypeByUserName($username)
    {
        $sql="SELECT type FROM user_validation 
            JOIN users ON user_validation.user_id=users.id 
            WHERE users.username=:un"
        ;
        $query=$this->connect()->prepare($sql);
        $query->execute([':un'=> $username]);
        if( $query->rowCount() > 0)
            return $query->fetchColumn();
    }
    public function getUserValidationByUserName($username)
    {
        $sql="SELECT data FROM user_validation 
            JOIN users ON user_validation.user_id=users.id 
            WHERE users.username=:un"
        ;
        $query=$this->connect()->prepare($sql);
        $query->execute([':un'=> $username]);
        if( $query->rowCount() > 0)
            return $query->fetchColumn();
    }
    public function userIsUser()
    {
        //return $this->getUserRole()=="user";
        return $this->getDataSession('role')=="user";
    }
    public function userIsAdmin()
    {
        //return $this->getUserRole()=="admin";
        return $this->getDataSession('role')=="admin";
    }
    public function userIsDev()
    {
        //return $this->getUserRole()=="dev";
        return $this->getDataSession('role')=="dev";
    }
    
}

?>