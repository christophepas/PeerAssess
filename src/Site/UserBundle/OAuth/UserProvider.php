<?php

namespace Site\UserBundle\OAuth;

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Peerassess\CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Peerassess\CoreBundle\Entity\UserType;

/**
 * Loading and ad-hoc creation of a user by an OAuth sign-in provider account.
 *
 * @author Fabian Kiss <fabian.kiss@ymc.ch>
 */
class UserProvider extends FOSUBUserProvider
{
	/**
	 * {@inheritDoc}
	 */
	public function loadUserByOAuthUserResponse(UserResponseInterface $response)
	{
		try {
			return parent::loadUserByOAuthUserResponse($response);
		} catch (UsernameNotFoundException $e) {
			if (null === $user = $this->userManager->findUserByEmail($response->getEmail())) {
				return $this->createUserByOAuthUserResponse($response);
			}
			return $this->updateUserByOAuthUserResponse($user, $response);
		}
	}
	/**
	 * {@inheritDoc}
	 */
	public function connect(UserInterface $user, UserResponseInterface $response)
	{
		$providerName = $response->getResourceOwner()->getName();
		$uniqueId = $response->getUsername();
		$user->addOAuthAccount($providerName, $uniqueId);
		$this->userManager->updateUser($user);
	}
	/**
	 * Ad-hoc creation of user
	 *
	 * @param UserResponseInterface $response
	 *
	 * @return User
	 */
	protected function createUserByOAuthUserResponse(UserResponseInterface $response)
	{
		$user = $this->userManager->createUser();
		//TODO : for now, github connect -> candidate
		$user->setType(UserType::CANDIDATE);
		$user->addRole("ROLE_CANDIDATE");

		//HIWOAuth ne prend pas encore bien les mails github -> requête à la mano.
		$accessToken = $response->getAccessToken();
		$url = 'https://api.github.com/user/emails?access_token=' . $accessToken;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Peerassess');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($content, true);

		$email = $result[0]['email'];
		$user->setEmail($email);

		$this->updateUserByOAuthUserResponse($user, $response);
		// set default values taken from OAuth sign-in provider account
		// if (null !== $email = $response->getEmail()) {
		// 	$user->setEmail($email);
		// }
		if (null === $this->userManager->findUserByUsername($response->getNickname())) {
			$user->setUsername($response->getNickname());
		}
		$user->setEnabled(true);
		return $user;
	}
	/**
	 * Attach OAuth sign-in provider account to existing user
	 *
	 * @param FOSUserInterface      $user
	 * @param UserResponseInterface $response
	 *
	 * @return FOSUserInterface
	 */
	protected function updateUserByOAuthUserResponse(FOSUserInterface $user, UserResponseInterface $response)
	{
		$providerName = $response->getResourceOwner()->getName();
		$providerNameSetter = 'set' . ucfirst($providerName) . 'Id';
		$user->$providerNameSetter($response->getUsername());
		if(!$user->getPassword()) {
			// generate unique token
			$secret = md5(uniqid(rand(), true));
			$user->setPassword($secret);
		}
		return $user;
	}
}
