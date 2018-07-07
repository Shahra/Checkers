<?php

class LoginController extends BaseController
{
	public function index() {
		if(LoginService::loggedIn()) {
			header( 'Location: ' . __SITE_URL . '/index.php?rt=checkers' );
			exit();
		}
		$this->registry->template->message = '';
		$this->registry->template->show('login_index');
	}

	public function processLogin() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}

		if(!isset($_POST['username']) || !isset($_POST['password'])) {
			$this->registry->template->message = 'Trebate unijeti korisničko ime i lozinku.';
			$this->registry->template->show('login_index');
			exit();
		}

		if(!LoginService::isUsernameValid($_POST['username'])) {
			$this->registry->template->message = 'Korisničko ime se treba sastojati od 6 i 20 slova.';
			$this->registry->template->show('login_index');
			exit();
		}

		$user = LoginService::getUserFromDatabase($_POST['username']);

		if($user === false) {
			$this->registry->template->message = 'Korisnik s tim imenom ne postoji.';
			$this->registry->template->show('login_index');
			exit();
		}

		else if($user->has_registered === 0) {
			$this->registry->template->message = 'Korisnik s tim imenom se nije još registrirao. Provjerite e-mail.';
			$this->registry->template->show('login_index');
			exit();
		}

		else if(!password_verify($_POST['password'], $user->password_hash)) {
			$this->registry->template->message = 'Unesena lozinka nije ispravna.';
			$this->registry->template->show('login_index');
			exit();
		}

		else {
			$_SESSION['username'] = $_POST['username'];
			header( 'Location: ' . __SITE_URL . '/index.php?rt=checkers' );
			exit();
		}
	}

	public function newUser() {
		if(LoginService::loggedIn()) {
			header( 'Location: ' . __SITE_URL . '/index.php?rt=checkers' );
			exit();
		}

		$this->registry->template->message = '';
		$this->registry->template->show('login_newUser');
	}

	public function processNewUser() {

		if(!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email']) || $_POST['username'] === "" || $_POST['password'] === "" || $_POST['email'] === "") {
			$this->registry->template->message = 'Sva polja trebaju biti popunjena.';
			$this->registry->template->show('login_newUser');
			exit();
		}

		if(!LoginService::isUsernameValid($_POST['username'])) {
			$this->registry->template->message = 'Korisničko ime se treba sastojati od 6 i 20 slova.';
			$this->registry->template->show('login_newUser');
			exit();
		}

		else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$this->registry->template->message = 'E-mail adresa nije ispravna.';
			$this->registry->template->show('login_newUser');
			exit();
		}

		else {
			$user = LoginService::getUserFromDatabase($_POST['username']);
			if($user !== false) {
				$this->registry->template->message = 'Korisnik s tim imenom već postoji u bazi.';
				$this->registry->template->show('login_newUser');
				exit();
			}
			LoginService::sendRegistrationRequest();
			$this->registry->template->message = 'Odite na vaš e-mail i potvrdite registraciju. <br /> 
																					  Nakon toga ćete se moći ulogirati na početnoj stranici.';
			$this->registry->template->show('login_index');
			exit();
		}
	}

	public function confirmRegistration() {

		if($_GET['code'] === ""){
			$this->registry->template->message = 'Nije validan registracijski kod.';
			$this->registry->template->show('login_newUser');
			exit();
		}

		$user = LoginService::getUserFromDatabaseWithRegSeq($_GET['code']);

		if($user === false) {
			$this->registry->template->message = 'Nije validan registracijski kod.';
			$this->registry->template->show('login_newUser');
			exit();
		}

		else {
			LoginService::registerUser($user->username);
			$this->registry->template->show('login_requestSent');
			exit();
		}
	}

	public function logout() {
		session_start();
		session_destroy();
		header('Location: ' . __SITE_URL . '/index.php?rt=login/index');
	}
};

?>
