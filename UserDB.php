<?php

// TODO
// add methods for specific id queries
// fix some better exception handling than die()
// add method for chicking for user exist
// add method for adding user including nick
// change method for top 10 to output nick


/**
 * Class UserDB
 * @author Magnus Arvidsson
 */
class UserDB
{
    private static $username = "magarv_axeltimer";
    private static $password = "X1lP692G8rbMnQWQnNUB";
    private static $host = "localhost";
    private static $dbname = "magarv_axeltimer";

    private static $dbOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

    public static $db;

    public function __construct()
    {
        $dbUsername = self::$username;
        $dbPassword = self::$password;
        $dbHost = self::$host;
        $dbName = self::$dbname;

        try {
        $this->db = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8", 
                      $dbUsername, $dbPassword, self::$dbOptions);
        } catch (PDOException $e) {
            die("Could not connect to database: " . $e->getMessage());
        }

        // PDO throws exceptions
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // PDO fetches table rows as associative arrays
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function addUser($kthID, $nickname){
        if ($nickname === "") {
          die("Nickname must be at least 1 character long");
        }

        $query = "SELECT * FROM users WHERE nickname = :nickname";
        $queryParams = array(':nickname' => $nickname);

        try {
          $stmt = $this->db->prepare($query);
          $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
          die("Could not query database.");
        }

        $row = $stmt->fetch();
        if ($row) {
          die("Nickname already taken.");
        } else {
          $query = "INSERT INTO users ( kthID , nickname) VALUES ( :kthID , :nickname )";
          $queryParams = array(':kthID' => $kthID, ':nickname' => $nickname);

          try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
          } catch (PDOException $e) {
            die("Could not query database: " . $e.getMessage());
          }
        }
    }

    public function getUserID($kthID) {
        $query = "SELECT * FROM users WHERE kthID = :kthID";
        $queryParams = array(":kthID" => $kthID);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query database: " . $e.getMessage());
        }
        $row = $stmt->fetch();
        if ($row) {
            return $row['id'];
        } else {
          return NULL;
        }
    } 

    public function getUserNick($userID){
      $query = "SELECT * FROM users WHERE id = :userID";
      $queryParams = array(":userID" => $userID);

      try {
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute($queryParams);
      } catch (PDOException $e) {
        die("Could not query database while getting nick: " . $e.getMessage());
      }

      $row=$stmt->fetch();
      if ($row) {
        return $row['nickname'];
      } else {
        return NULL;
      }
    }

    public function getUser($kthID) {
        $query = "SELECT * FROM users WHERE kthID = :kthID";
        $queryParams = array(":kthID" => $kthID);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query database: " . $e.getMessage());
        }
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        } else {
          return NULL;
        }
      
    } 



    public function getActiveSession($userID){
        $query = "SELECT * FROM sessions WHERE userID = :userID AND stopTime IS NULL";
        $queryParams = array(':userID' => $userID);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query session database: " . $e.getMessage());
        }
        $row = $stmt->fetch();

        if ($row) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function addToTotalTime($startTime, $endTime, $userID){
        $timeDiff = $endTime - $startTime;
        $query = "UPDATE users SET totalTime = totalTime + :timeDiff WHERE id = :userID";
        $queryParams = array(':timeDiff' => $timeDiff, ":userID" => $userID);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query database while updating time: " . $e->getMessage());
        }
    }

    public function getRowByUserID($userID){
        $query = "SELECT * FROM users WHERE id = :userID";
        $queryParams = array(":userID" => $userID);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query user database while getting user time: " . $e->getMessage());
        }

        $row = $stmt->fetch();
        if ($row) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getRowByKthID($kthID){
        $query = "SELECT * FROM users WHERE id = :kthID";
        $queryParams = array(":kthID" => $kthID);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query user database while getting user time: " . $e->getMessage());
        }

        $row = $stmt->fetch();
        if ($row) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getTotalTimeByUserID($userID){
        $query = "SELECT totalTime FROM users WHERE id = :userID";
        $queryParams = array(":userID" => $userID);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query user database while getting user time: " . $e->getMessage());
        }

        $row = $stmt->fetch();
        if ($row) {
            return self::toReadableTime($row['totalTime']);
        } else {
            return NULL;
        }
    }



    public function deleteSession($sessionID) {
        $query = "DELETE FROM sessions WHERE id = :sessionID";
        $queryParams = array(":sessionID" => $sessionID);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query session database while deleting session : " . $e->getMessage());
        }
    }

    public function startSession($userID, $currentTime){
        $query = "INSERT INTO sessions (userID, startTime) VALUES (:userID, :currentTime)";
        $queryParams = array(':userID' => $userID, ':currentTime' => $currentTime);

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query database while adding session: " . $e->getMessage());
        }
    }

    public function finishSession($currentTime, $sessionID){
        $query = "UPDATE sessions SET stopTime = :currentTime WHERE id = :sessionID";
        $queryParams = array(":currentTime" => $currentTime, ":sessionID" => $sessionID);
        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($queryParams);
        } catch (PDOException $e) {
            die("Could not query database when finishing session: ". $e->getMessage());
        }
    }

    public function getTop10(){
      $query = "SELECT nickname, totalTime FROM users ORDER BY totalTime DESC LIMIT 10";
      $tableHTML = "";
      $index = 1;
      foreach ($this->db->query($query) as $row) {
        $time = self::toReadableTime($row['totalTime']);
        $tableHTML = $tableHTML . "<tr><td>{$index}</td><td>{$row['nickname']}</td><td>{$time}</td></tr>\n";
        $index += 1;
      }

      return $tableHTML;
    }

    private static function toReadableTime($seconds){
      $minute = 60;
      $hour = 3600;

      $noHours = (int)($seconds/$hour);
      $seconds = $seconds % $hour;
      $noMinutes = (int)($seconds/$minute);
      $seconds = $seconds % $minute;

      if ($noHours > 0) {
        $rt = "{$noHours} timmar, {$noMinutes} minuter, {$seconds} sekunder";
      } elseif ($noMinutes > 0) {
        $rt = "{$noMinutes} minuter, {$seconds} sekunder";
      } else {
        $rt = "{$seconds} sekunder";
      }

      return $rt;
    }
}


?>
