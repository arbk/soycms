<?php

class FbLoginLogic extends SOY2LogicBase {

	const FIELD_ID = "social_login_facebook_login";

	private $userDao;
	private $userAttrDao;

	function __construct(){
		$this->userDao = SOY2DAOFactory::create("user.SOYShop_UserDAO");
		$this->userAttrDao = SOY2DAOFactory::create("user.SOYShop_UserAttributeDAO");
	}

	function getUserByMailAddress($mailAddress){
		try{
			return $this->userDao->getByMailAddress($mailAddress);
		}catch(Exception $e){
			$user = new SOYShop_User();
			$user->setMailAddress($mailAddress);
			$user->setUserType(SOYShop_User::USERTYPE_REGISTER);
			return $user;
		}
	}

	function registUser(SOYShop_User $user){
		try{
			return $this->userDao->insert($user);
		}catch(Exception $e){
			return null;
		}
	}

	function saveFacebookId($userId, $facebookId){
		$attr = self::getAttributeObjectByUserId($userId);
		$attr->setValue($facebookId);
		try{
			$this->userAttrDao->insert($attr);
		}catch(Exception $e){
			try{
				$this->userAttrDao->update($attr);
			}catch(Exception $e){
				//
			}
		}
	}

	function getFacebookIdByUserId($userId){
		return self::getAttributeObjectByUserId($userId)->getValue();
	}

	private function getAttributeObjectByUserId($userId){
		try{
			return $this->userAttrDao->get($userId, self::FIELD_ID);
		}catch(Exception $e){
			$attr = new SOYShop_UserAttribute();
			$attr->setUserId($userId);
			$attr->setFieldId(self::FIELD_ID);
			return $attr;
		}
	}
}
