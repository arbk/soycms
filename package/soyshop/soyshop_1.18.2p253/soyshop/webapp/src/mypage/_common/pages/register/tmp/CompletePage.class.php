<?php 
class CompletePage extends MainMyPagePageBase{
		
	function __construct(){
		
		$register = false;
    	
    	if(isset($_GET["q"])){
    		$register = $this->executeRegister($_GET["q"]);
    	}
    	
    	parent::__construct();
		
		//success
		$this->addModel("register_success", array(
			"visible" => ($register)
		));

		$loginUrl = soyshop_get_mypage_url() . "/login";
		if(isset($_GET["r"])){
			$loginUrl .= "?r=" . $_GET["r"];
		}
		$this->addLink("login_link", array(
			"link" => $loginUrl
		));

		
		//failure
		$this->addModel("register_failure", array(
			"visible" => (!$register)
		));
		
		$this->addLink("register_link", array(
			"link" => soyshop_get_mypage_url() . "/register"
		));
	}
	
	function executeRegister($query){
		
		$userDAO = SOY2DAOFactory::create("user.SOYShop_UserDAO");
		$tokenDAO = SOY2DAOFactory::create("user.SOYShop_UserTokenDAO");
			
		try{
			$token = $tokenDAO->getByToken($query);
			$user = $userDAO->getById($token->getUserId());

			//user type
			if($user->getUserType() != SOYShop_User::USERTYPE_TMP){
				throw new Exception(MessageManager::get("NO_PROVISIONAL_REGISTRATION"));
			}
			
			//time limit
			if($token->getLimit() < time()){
				throw new Exception(MessageManager::get("TERM_OF_VALIDITY"));
			}
			
			$user->setUserType(SOYShop_User::USERTYPE_REGISTER);
			$user->setRealRegisterDate(time());

			$userDAO->update($user);
			$this->sendRegisterMail($user);
			
			$token->delete();
			
		}catch(Exception $e){
			return false;
		}
		
		return true;
	}
	
	function sendRegisterMail(SOYShop_User $user){

		$mailLogic = SOY2Logic::createInstance("logic.mail.MailLogic");
		$config = $mailLogic->getMyPageMailConfig("register");
		
		SOY2::import("domain.order.SOYShop_Order");
		//convert title
		$title = $mailLogic->convertMailContent($config["title"], $user, new SOYShop_Order());

		//convert content
		$mailBody = $config["header"] . "\n" . $config["footer"];
		$content  = $mailLogic->convertMailContent($mailBody, $user, new SOYShop_Order());

		try{
			$mailLogic->sendMail($user->getMailAddress(), $title, $content);
		}catch(Exception $e){
			
		}	
	}
}
?>