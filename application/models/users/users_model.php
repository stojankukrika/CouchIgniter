<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 1/29/13
 * Time: 3:49 PM
 * To change this template use File | Settings | File Templates.
 */
class Users_model extends CI_Model
{

    private $prevDB;

    private function _setDB(){
        $this->prevDB = $this->couchsag->sag->currentDatabase();
        $this->couchsag->sag->setDatabase('users');

    }
    private function _restoreDB(){
        $this->couchsag->sag->setDatabase($this->prevDB);
    }
    public function getUsersByUsername(){
        $this->_setDB();
        $seq = $this->couchsag->get("/_design/newFilter/_view/userByUsername");
        $this->_restoreDB();
        return $seq->rows;
    }

    public function getUsersByEmail(){
        $this->_setDB();
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            $this->_restoreDB();
            return $usersDoc->userByEmail;
        }
        $this->_restoreDB();
        return array();
    }

    public function getUserByEmail($email){
        $this->_setDB();
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            $user = $usersDoc->userByEmail->$email;
            $this->_restoreDB();
            return $user;
        }
        $this->_restoreDB();
        return array();
    }

    public function initDoc(){
        echo "<pre>";
        $this->couchsag->sag->setDatabase('users');

        try{
            echo "is Users doc presesnt?\n";
            $doc = $this->couchsag->get("users");
        }catch(Exception $e){};
        if(!$doc){
            $data = array("_id" => "users", "docType" => "users", "userByEmail" => new stdClass(), "userId"=> 1);
            echo "createing users\n";
                $this->couchsag->create($data);
            echo "Created them\n";
        }else{
            echo "users doc found, leaving untouched\n";
        }

        $doc = false;

        try{
            echo "is userLogins doc presesnt?\n";
            $doc = $this->couchsag->get("userLogins");
        }catch(Exception $e){};
        if(!$doc){
            $data = array("_id" => "userLogins", "docType" => "userLogins", "logins" => array());
            echo "createing userLogins\n";
            $this->couchsag->create($data);
            echo "Created them\n";
        }else{
            echo "userLogins doc found, leaving untouched\n";
        }

        $doc = false;
        try{
            echo "is gnuGamesAvail doc presesnt?\n";
            $doc = $this->couchsag->get("gnuGamesAvail");
        }catch(Exception $e){};
        if(true || !$doc){
            $data = array("_id" => "gnuGamesAvail", "docType" => "gnuGamesAvail", "games" => new stdClass());
            echo "createing gnuGamesAvail\n";
            $this->couchsag->create($data);
            echo "Created them\n";
        }else{
            var_dump($doc);
            echo "gnuGamesAvail doc found, leaving untouched\n";
        }

        $users = new StdClass();
        $users->map = <<<aHEREMAP
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    for(var email in doc.userByEmail){
                        emit(email,doc.userByEmail[email]);
                    }
                }
            }
        }
aHEREMAP;
        $userById = new stdClass();
        $userById->map = <<<byId
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    var aThing;
                    for(var email in doc.userByEmail){
                        aUser = doc.userByEmail[email];
                        theUser = {};
                        for(x in aUser){
                            theUser[x] = aUser[x];
                        }
                        theUser.email = email;
                        emit(doc.userByEmail[email].id,theUser);
                    }
                }
            }
        }
byId;
        $userByUsername = new stdClass();
        $userByUsername->map = <<<byUsername
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    for(var email in doc.userByEmail){
                        aUser = doc.userByEmail[email];
                        theUser = {};
                        for(x in aUser){
                            theUser[x] = aUser[x];
                        }
                        theUser.email = email;
                        emit(doc.userByEmail[email].username,theUser);
                    }
                }
            }
        }

byUsername;
        $getAvailGames = new stdClass();
        $getAvailGames->map = "function(doc){if(doc.docType == 'gamesAvail'){if(doc.games){for(var i in doc.games){emit(doc.games[i],doc.games[i]);}}}}";

        $gnuGetAvailGames = new stdClass();
        $gnuGetAvailGames->map = <<<gamesAvail
            function(doc){
                if(doc.docType == 'gnuGamesAvail'){
                    if(doc.games){
                        for(var i in doc.games){
                            emit(i,doc.games[i]);
                        }
                    }
                }
            }

gamesAvail;


        $views['userByEmail'] = $users;
        $views['userById'] = $userById;
        $views['userByUsername'] = $userByUsername;
        $views['getAvailGames'] = $getAvailGames;
        $views['gnuGetAvailGames'] = $gnuGetAvailGames;

//        $data = array("_id" => "_design/newFilter", "views" => $views, "filters" => $filters, "updates"=> $updates);
        $data = array("_id" => "_design/newFilter", "views" => $views);

        try{
            $doc = $this->couchsag->get("_design/newFilter");
        }catch(Exception $e){};
        if($doc){
            echo "design doc found, deleting.\n";
            $delDoc = $this->couchsag->delete($doc->_id,$doc->_rev);
            echo "deleted\n";
        }
        echo "creating design doc\n";
        $this->couchsag->create($data);
        echo "did it";
        return;

        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            $email = "dave.rodal@gmail.com";
            $user = new stdClass();
            $user->id = $usersDoc->userId++;
            $user->password = "2havefun";
            var_dump($usersDoc->userByEmail);
            $usersDoc->userByEmail->$email = $user;
            echo "<pre>";
            var_dump($usersDoc);
            $this->couchsag->update($usersDoc->_id, $usersDoc);
            return $user;
        }

    }
    public function addUser($email, $password, $username){
        $this->_setDB();
        $strikes = 0;
        while($strikes < 3){
            $usersDoc = $this->couchsag->get("users");
    //        $usersDoc->_rev = "";
            if($usersDoc->docType == "users"){
                $users = $usersDoc->userByEmail;
                if($users->$email){
                    return "Email already used: $email";
                }

                foreach($users as  $user){
                    if($user->username == $username){
                        return "Username already used: $username";
                    }
                }
                $user = new stdClass();
                $user->id = $usersDoc->userId++;
                $user->username = $username;
                $user->password = $password;
                $usersDoc->userByEmail->$email = $user;
                if(isset($usersDoc->userById)){
                    unset($usersDoc->userById);
                }
                $ret = $this->couchsag->update($usersDoc->_id, $usersDoc);
                if($ret && $ret->ok){
                    return false;
                }
            }
        }
        return "Cannot save $strikes strikes";
    }

    public function isValidLogin($email, $password){
        $this->_setDB();
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            if(isset($usersDoc->userByEmail->$email))
            {
                if($usersDoc->userByEmail->$email->password == $password){
                    $this->_restoreDB();
                    return true;
                }
            }
        }
        $this->_restoreDB();
        return false;
    }
    public function addGame($games){
        $this->_setDB();
        $doc = $this->couchsag->get("gnuGamesAvail");
        if($doc->docType == "gnuGamesAvail"){
            foreach($games as $name => $game)
            $doc->games->$name = $game;
        }
        $ret = $this->couchsag->update($doc->_id, $doc);
        $this->_restoreDB();
    }
    public function deleteGame($killGame){
        if(!$killGame){
            return false;
        }
        $this->_setDB();
        $doc = $this->couchsag->get("gnuGamesAvail");
        if(!$doc->docType == "gnuGamesAvail"){
            return false;
        }
        unset($doc->games->$killGame);
        $ret = $this->couchsag->update($doc->_id, $doc);
        $this->_restoreDB();
    }
	public function userLoggedIn($user){
		$this->_setDB();
		$doc = $this->couchsag->get("userLogins");
		$gnu = new stdClass();
		$gnu->name = $user;
		$gnu->time = date("Y-m-d H:i:s");
		$doc->logins[] = $gnu;
		$this->couchsag->update($doc->_id, $doc);
		$this->_restoreDB();
	}
    public function getAvailGames(){
        $this->_setDB();
        $seq = $this->couchsag->get("/_design/newFilter/_view/gnuGetAvailGames");
        $this->_restoreDB();
        $rows = $seq->rows;
        $games = [];
        foreach($rows as $row){
            $game = $row->value;
            $game->key = $row->key;
            $games[] = $game;
        }

        return $games;
    }
    public function getGame($gameName){
        $games = $this->getAvailGames();
        foreach($games as $game){
            if($gameName == $game->key){
                return $game;
            }
        }
        return false;
    }

    public function getLogins(){
        $this->_setDB();
        $logins = $this->couchsag->get("userLogins");
        $this->_restoreDB();
        return $logins;

    }

}
